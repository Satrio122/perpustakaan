@extends('admin.layout.main')

@section('title', 'Master User')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box" style="cursor: pointer;"
                onclick="tampilUserModal()">
                <span class="info-box-icon bg-dark">
                    <i class="fas fa-plus"></i>
                </span>
                <div class="info-box-content d-flex align-items-center justify-content-center">
                    <span class="info-box-text">Tambah User</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama User</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $item)
                                @php($imageUrl = asset('storage/' . ($item->image ?: 'profil-pic/default.jpg')))
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="nama-user">{{ $item->nama }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->is_active ? 'primary' : 'secondary' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->role_id == 1 ? 'success' : 'info' }}">
                                            {{ $item->role_id == 1 ? 'Administrator' : 'Member' }}
                                        </span>
                                    </td>
                                    <td>
                                        <img src="{{ $imageUrl }}" alt="Foto {{ $item->nama }}"
                                            width="50" height="50" class="img-circle"
                                            style="object-fit: cover;">
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-xs btn-info btn-detail-user"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama }}"
                                            data-alamat="{{ $item->alamat }}"
                                            data-email="{{ $item->email }}"
                                            data-active="{{ $item->is_active }}"
                                            data-role="{{ $item->role_id }}"
                                            data-image="{{ $imageUrl }}"
                                            data-update-action="{{ route('admin.master.user.update', $item->id) }}"
                                            data-toggle="tooltip" title="Detail user" aria-label="Detail user">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama }}"
                                            data-alamat="{{ $item->alamat }}"
                                            data-email="{{ $item->email }}"
                                            data-active="{{ $item->is_active }}"
                                            data-role="{{ $item->role_id }}"
                                            data-image="{{ $imageUrl }}"
                                            data-update-action="{{ route('admin.master.user.update', $item->id) }}"
                                            data-toggle="tooltip" title="Edit user" aria-label="Edit user"
                                            onclick="tampilUserModal('{{ $item->id }}', this)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.master.user.destroy', $item->id) }}"
                                            method="POST" class="d-inline hapus-data">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                data-toggle="tooltip" title="Hapus user" aria-label="Hapus user">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.master.user.resetPassword', $item->id) }}"
                                            method="POST" class="d-inline reset-password">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-xs btn-info"
                                                data-toggle="tooltip" title="Reset password" aria-label="Reset password">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1"
    role="dialog" aria-labelledby="userModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="userModalLabel">
                    Tambah User Baru
                </h5>

                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <form id="userForm"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                <input type="hidden" name="_method" id="userMethod">

                <div class="modal-body">

                    <div class="form-group">

                        <label for="nama">Nama User</label>

                        <input type="text"
                            class="form-control"
                            id="nama"
                            name="nama"
                            placeholder="Nama User"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="alamat">Alamat</label>

                        <input type="text"
                            class="form-control"
                            id="alamat"
                            name="alamat"
                            placeholder="Alamat"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="email">Email</label>

                        <input type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Email"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="is_active">Status</label>

                        <div class="custom-control custom-radio">

                            <input type="radio"
                                id="aktif"
                                name="is_active"
                                value="1"
                                class="custom-control-input"
                                checked>

                            <label class="custom-control-label"
                                for="aktif">
                                Aktif
                            </label>

                        </div>

                        <div class="custom-control custom-radio">

                            <input type="radio"
                                id="tidakAktif"
                                name="is_active"
                                value="0"
                                class="custom-control-input">

                            <label class="custom-control-label"
                                for="tidakAktif">
                                Tidak Aktif
                            </label>

                        </div>

                    </div>

                    <div class="form-group">

                        <label for="role_id">Role</label>

                        <select class="form-control"
                            name="role_id"
                            id="role_id"
                            required>

                            <option value="1">
                                Administrator
                            </option>

                            <option value="2">
                                Anggota
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="image">Gambar</label>

                        <input type="hidden"
                            name="oldImage"
                            id="oldImage">

                        <img class="img-preview img-fluid mb-3 col-sm-5 d-block"
                            src="{{ asset('storage/profil-pic/default.jpg') }}"
                            id="imagePreview">

                        <div class="input-group">

                            <div class="custom-file">

                                <input type="file"
                                    class="custom-file-input"
                                    id="image"
                                    name="image"
                                    accept="image/xpng,image/jpg,image/jpeg"
                                    onchange="previewImage()">

                                <label class="custom-file-label"
                                    for="image">
                                    Pilih file...
                                </label>

                            </div>

                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-none"
                            id="resetImage" onclick="resetImage()">
                            Reset Image
                        </button>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Tutup
                    </button>

                    <button type="submit"
                        class="btn btn-primary"
                        id="userSubmitButton">
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
    $(function() {
        $('#example1').DataTable({
            responsive: true,
            autoWidth: false,
        });

        $('[data-toggle="tooltip"]').tooltip();
    });

    function tampilUserModal(id = null, button = null) {
        const form = $('#userForm');
        const isEdit = id !== null;
        const data = isEdit ? $(button).data() : {};

        $('#userModalLabel').text(isEdit ? 'Edit User' : 'Tambah User Baru');
        form.attr('action', isEdit ? data.updateAction : "{{ route('admin.master.user.store') }}");
        $('#userMethod').val(isEdit ? 'PUT' : '');
        $('#userSubmitButton').text(isEdit ? 'Ubah' : 'Simpan').show();

        $('#nama').val(isEdit ? data.nama : '').prop('readonly', false);
        $('#alamat').val(isEdit ? data.alamat : '').prop('readonly', false);
        $('#email').val(isEdit ? data.email : '').prop('readonly', false);
        $('#aktif').prop('checked', isEdit ? String(data.active) === '1' : true);
        $('#tidakAktif').prop('checked', isEdit ? String(data.active) === '0' : false);
        $('#role_id').val(isEdit ? data.role : '2').prop('disabled', false);
        $('#oldImage').val(isEdit ? data.image : '');
        const defaultImage = "{{ asset('storage/profil-pic/default.jpg') }}";
        const previewImageUrl = isEdit ? data.image : defaultImage;
        $('#imagePreview').attr('src', previewImageUrl).data('original-image', previewImageUrl);
        $('#image').val('').prop('disabled', false);
        $('.custom-file-label').text('Pilih file...');
        $('#resetImage').addClass('d-none');

        if (isEdit) {
            $('#userModal').data('mode', 'edit');
        } else {
            $('#userModal').data('mode', 'create');
        }

        $('#userModal').modal('show');
    }

    $('.btn-detail-user').on('click', function() {
        const data = $(this).data();

        tampilUserModal(data.id, this);
        $('#userModalLabel').text('Detail User');
        $('#userSubmitButton').hide();
        $('#nama, #alamat, #email').prop('readonly', true);
        $('#aktif, #tidakAktif, #role_id, #image').prop('disabled', true);
    });

    $(document).on('submit', 'form.hapus-data', function(event) {
        if (!confirm('Yakin ingin menghapus data user ini?')) {
            event.preventDefault();
        }
    });

    $(document).on('submit', 'form.reset-password', function(event) {
        if (!confirm('Yakin ingin mereset password user ini?')) {
            event.preventDefault();
        }
    });

    function previewImage() {

        const image = $('#image');
        const imgPreview = $('#imagePreview');

        const file = image[0].files[0];

        const reader = new FileReader();

        reader.onload = function(event) {
            imgPreview.attr('src', event.target.result);
        }

        if (file) {
            reader.readAsDataURL(file);
            $('#resetImage').removeClass('d-none');
        }
    }

    function resetImage() {
        const imagePreview = $('#imagePreview');

        $('#image').val('');
        imagePreview.attr('src', imagePreview.data('original-image') || "{{ asset('storage/profil-pic/default.jpg') }}");
        $('.custom-file-label').text('Pilih file...');
        $('#resetImage').addClass('d-none');
    }
</script>

@endpush