<?php

namespace App\Http\Controllers\Web;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests\SantriRequest;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SantriController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = Santri::query();

        if (auth()->user()->isSantri()) {
            $query->whereHas('user', function ($q) {
                $q->where('id', auth()->id());
            });
        } elseif (! auth()->user()->isSuperAdmin()) {
            $query->whereDoesntHave('user', function ($q) {
                $q->where('role', 'SuperAdmin');
            });
        }

        $data = $query->latest()->paginate(10);
        $totalCount = $query->count();
        $keyword = $request->keyword;

        if ($keyword) {
            $searchQuery = Santri::where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('address', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%");
            });

            if (auth()->user()->isSantri()) {
                $searchQuery->whereHas('user', function ($q) {
                    $q->where('id', auth()->id());
                });
            } elseif (! auth()->user()->isSuperAdmin()) {
                $searchQuery->whereDoesntHave('user', function ($q) {
                    $q->where('role', 'SuperAdmin');
                });
            }

            $totalCount = $searchQuery->count();
            $data = $searchQuery->latest()->paginate(10);
        }

        return view('santri.index', compact('data', 'totalCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('santri.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(SantriRequest $request)
    {
        $santri = new Santri;

        if ($request->hasFile('photo')) {
            $validatedData = $request->validated();
            $file = $request->photo;
            $input['photo'] = 'santri-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/photo');

            File::exists($destinationPath) or File::makeDirectory($destinationPath);

            $destinationPath = public_path('storage/photo');
            $file->move($destinationPath, $input['photo']);
            $validatedData['photo'] = $input['photo'];
            $created = $santri->create($validatedData);
            // Buat user otomatis untuk Santri baru
            try {
                User::create([
                    'email' => 'santri_'.Str::random(5).'@ppm.am',
                    'password' => Hash::make(substr($validatedData['phone'] ?? 'password', -6)),
                    'role' => 'Santri',
                    'santri_id' => $created->id,
                ]);
            } catch (\Exception $e) {
                // Jika pembuatan user gagal, tetap lanjut tapi catat log
                LogActivity::addToLog('Gagal membuat user otomatis untuk santri: '.($created->id ?? 'unknown'));
            }
        } else {
            $validatedData = $request->validated();
            $created = $santri->create($validatedData);
            // Buat user otomatis untuk Santri baru
            try {
                User::create([
                    'email' => 'santri_'.Str::random(5).'@ppm.am',
                    'password' => Hash::make(substr($validatedData['phone'] ?? 'password', -6)),
                    'role' => 'Santri',
                    'santri_id' => $created->id,
                ]);
            } catch (\Exception $e) {
                LogActivity::addToLog('Gagal membuat user otomatis untuk santri: '.($created->id ?? 'unknown'));
            }
        }

        LogActivity::addToLog('Tambah Data Santri');

        return redirect()->route('santri.index')
            ->with('alert', 'Santri baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        $santri = Santri::findOrFail($id);
        $this->authorizeSantriAccess($santri);

        return view('santri.show', compact('santri'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $santri = Santri::findOrFail($id);

        $this->authorizeSantriAccess($santri, 'mengedit');

        return view('santri.edit', ['santri' => $santri]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Santri  $santri
     * @return Response
     */
    public function update(SantriRequest $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $this->authorizeSantriAccess($santri, 'mengubah');

        if ($request->hasFile('photo')) {
            $validatedData = $request->validated();
            $filePath = public_path('storage/photo/'.$santri->photo);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $file = $request->photo;
            $input['photo'] = 'santri-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/photo');
            File::exists($destinationPath) or File::makeDirectory($destinationPath);
            $file->move($destinationPath, $input['photo']);
            $validatedData['photo'] = $input['photo'];
            $santri->update($validatedData);
        } else {
            $validatedData = $request->validated();
            $santri->update($validatedData);
        }

        LogActivity::addToLog('Edit Data Santri');

        return redirect()->route('santri.index')
            ->with('alert', 'Data berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        if (auth()->user()->isSantri()) {
            abort(403, 'Santri tidak dapat menghapus data.');
        }

        if (Gate::allows('admin')) {
            $santri = Santri::findOrFail($id);
            $user = User::where('santri_id', $id)->first();

            if (! auth()->user()->isSuperAdmin() && $user && $user->isSuperAdmin()) {
                abort(403, 'Anda tidak memiliki hak akses untuk menghapus data SuperAdmin.');
            }

            if (auth()->user() == $user) {
                return redirect()->back()
                    ->with('alert', 'Gagal menghapus data sendiri.');
            }

            $filePath = public_path('storage/photo/'.$santri->photo);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $santri->delete();

            LogActivity::addToLog('Hapus Data Santri');

            return redirect()->route('santri.index')
                ->with('alert', 'Data berhasil dihapus.');
        }

        abort(403);
    }

    private function authorizeSantriAccess(Santri $santri, $action = 'melihat')
    {
        if (auth()->user()->isSantri()) {
            $userSantri = auth()->user()->santris;
            if (! $userSantri || $userSantri->id !== $santri->id) {
                abort(403, "Anda tidak memiliki hak akses untuk {$action} data santri lain.");
            }
        }

        if (! auth()->user()->isSuperAdmin() && $santri->user && $santri->user->isSuperAdmin()) {
            abort(403, "Anda tidak memiliki hak akses untuk {$action} data SuperAdmin.");
        }
    }
}
