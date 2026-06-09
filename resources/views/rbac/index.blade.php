@extends('layouts.home')
@section('title_page','Role Based Access Control')
@section('content')

    <div class="table-responsive mb-4">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th width="20%">Role</th>
                    <th>Deskripsi Akses</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role => $description)
                    <tr>
                        <td><span class="badge badge-primary">{{ $role }}</span></td>
                        <td>{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th>Menu/Fitur</th>
                    @foreach ($roles as $role => $description)
                        <th width="13%">{{ $role }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission => $definition)
                    <tr>
                        <td>{{ $definition['label'] }}</td>
                        @foreach ($roles as $role => $description)
                            <td align="center">
                                @php
                                    $allowed = optional(optional($matrix->get($permission))->get($role))->allowed ?? false;
                                    $locked = \App\Models\RbacPermission::locked($permission, $role);
                                @endphp
                                @if ($locked)
                                    <button type="button" class="btn btn-sm btn-success" disabled title="Dikunci">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-sm rbac-toggle {{ $allowed ? 'btn-success' : 'btn-secondary' }}"
                                        data-permission="{{ $permission }}"
                                        data-role="{{ $role }}"
                                        title="Ubah hak akses">
                                        <i class="fas {{ $allowed ? 'fa-check' : 'fa-times' }}"></i>
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('script')
    <script>
        $('.rbac-toggle').on('click', function () {
            const button = $(this);

            button.prop('disabled', true);

            $.ajax({
                url: '{{ route('rbac.toggle') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    permission: button.data('permission'),
                    role: button.data('role')
                },
                success: function (response) {
                    const icon = button.find('i');

                    button.toggleClass('btn-success', response.allowed);
                    button.toggleClass('btn-secondary', !response.allowed);
                    icon.toggleClass('fa-check', response.allowed);
                    icon.toggleClass('fa-times', !response.allowed);
                },
                error: function (xhr) {
                    alert(xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Gagal memperbarui hak akses.');
                },
                complete: function () {
                    button.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
