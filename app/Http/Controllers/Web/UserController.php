<?php

namespace App\Http\Controllers\Web;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::with('santris');

        // Santri hanya bisa lihat akunnya sendiri
        if (auth()->user()->role == 'Santri') {
            $query->where('id', auth()->id());
        }
        // SuperAdmin, Administrator, Pengurus bisa lihat semua

        $data = $query->latest()->paginate(10);
        $keyword = $request->keyword;

        if ($keyword) {
            $query = User::with('santris');

            // Santri hanya bisa lihat akunnya sendiri
            if (auth()->user()->role == 'Santri') {
                $query->where('id', auth()->id());
            }

            $data = $query->where('email', 'LIKE', "%$keyword%")
                    ->orWhere('role', 'LIKE', "%$keyword%")
                    ->orWhereHas('santris', function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', "%$keyword%");
                    })
                    ->latest()
                    ->paginate(10);
        }

        return view('user.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Santri::whereDoesntHave('user', function ($q) {
            $q->where('role', 'SuperAdmin');
        })->get();
        return view('user.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User;
        $validatedData              = $request->validated();
        $validatedData['password']  = Hash::make($request->password);
        $user->create($validatedData);

        LogActivity::addToLog('Tambah Data Pengguna');
        return redirect()->route('pengguna.index')
            ->with('alert', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Santri hanya bisa edit akunnya sendiri
        if (auth()->user()->role == 'Santri') {
            if (auth()->id() !== $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengedit akun lain.');
            }
        } elseif (!Gate::allows('admin')) {
            // Non-admin dan non-Santri tidak boleh akses
            abort(403);
        }

        $data = Santri::whereDoesntHave('user', function ($q) {
            $q->where('role', 'SuperAdmin');
        })->get();
        return view('user.edit', compact('user', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\UserRequest  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Santri hanya bisa update akunnya sendiri
        if (auth()->user()->role == 'Santri') {
            if (auth()->id() !== $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengubah akun lain.');
            }
        } elseif (!Gate::allows('admin')) {
            // Non-admin dan non-Santri tidak boleh akses
            abort(403);
        }

        $validatedData = $request->all();
        // Format email dengan @ppm.am jika belum ada domain
        if (!str_contains($validatedData['email'], '@')) {
            $validatedData['email'] = $validatedData['email'] . '@ppm.am';
        }
        $validatedData['password'] = Hash::make($request->password);
        $user->update($validatedData);

        LogActivity::addToLog('Edit Data Pengguna');
        return redirect()->route('pengguna.index')
            ->with('alert', 'Pengguna berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Santri tidak boleh delete
        if (auth()->user()->role == 'Santri') {
            abort(403, 'Santri tidak dapat menghapus data pengguna.');
        }

        if (Gate::allows('admin')) {
            $user = User::findOrFail($id);

            if (auth()->user() == $user) {
                return redirect()->back()
                    ->with('alert','Gagal menghapus data sendiri.');
            }

            $user->delete();

            LogActivity::addToLog('Hapus Data Pengguna');
            return redirect()->route('pengguna.index')
                ->with('alert','Pengguna berhasil dihapus.');
        }
        abort(403);
    }
}
