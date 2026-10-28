@extends('admin.layout.main')

@section('title', 'Profil Saya')

@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-md-8 col-lg-6">

            <!-- general form elements -->

            <div class="card card-primary">

                <div class="card-header">

                    <h3 class="card-title">Profil Saya</h3>

                </div>

                <!-- /.card-header -->
                <!-- form start -->

                <form role="form" action="{{ route('admin.profil.update') }}"
                    method="post" enctype="multipart/form-data"
                    id="profilForm">

                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <div class="form-group">

                            <label for="nama">Nama</label>

                            <input type="text"
                                class="form-control @error('nama') is-invalid @enderror"
                                name="nama"
                                id="nama"
                                placeholder="Nama Lengkap"
                                value="{{ Auth::user()->nama }}"
                                data-initial-value="{{ Auth::user()->nama }}"
                                readonly>

                            @error('nama')
                            <span id="nama-error"
                                class="error invalidfeedback">{{ $message }}</span>
                            @enderror

                        </div>

                        <div class="form-group">

                            <label for="alamat">Alamat</label>

                            <input type="text"
                                class="form-control @error('alamat') is-invalid @enderror"
                                name="alamat"
                                id="alamat"
                                placeholder="Alamat"
                                value="{{ Auth::user()->alamat }}"
                                data-initial-value="{{ Auth::user()->alamat }}"
                                readonly>

                            @error('alamat')
                            <span id="alamat-error"
                                class="error invalidfeedback">{{ $message }}</span>
                            @enderror

                        </div>

                        <div class="form-group">

                            <label for="email">Email</label>

                            <input type="email"
                                class="form-control"
                                name="email"
                                id="email"
                                placeholder="Email"
                                value="{{ Auth::user()->email }}"
                                data-initial-value="{{ Auth::user()->email }}"
                                readonly>

                        </div>

                        <div class="form-group">

                            <label for="image">Gambar</label>

                            <input type="hidden"
                                name="oldImage"
                                value="{{ Auth::user()->image }}">

                            <img class="img-preview img-fluid mb-3 col-sm-5 d-block"
                                src="{{ asset('storage/' . Auth::user()->image) }}">

                            <div class="input-group @error('image') is-invalid @enderror">

                                <div class="custom-file">

                                    <input type="file"
                                        class="custom-file-input @error('image') is-invalid @enderror"
                                        id="image"
                                        name="image"
                                        data-defaultsrc="{{ Auth::user()->image }}"
                                        accept="image/png,image/jpeg,image/jpg"
                                        onchange="previewImage()">

                                    <label class="custom-file-label"
                                        for="image"
                                        title="Pilih file...">
                                        <i class="fas fa-upload" aria-hidden="true"></i>
                                        <span class="sr-only">Pilih file...</span>
                                    </label>

                                </div>

                            </div>

                            @error('image')
                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>
                            @enderror

                        </div>

                    </div>

                    <!-- /.card-body -->

                    <div class="card-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            id="toggleButton">

                            Klik untuk Ubah Profil

                        </button>

                        <button type="button"
                            class="btn btn-danger d-none"
                            id="batal">

                            Batal

                        </button>

                    </div>

                </form>

            </div>

            <!-- /.card -->

        </div>

        <!-- /.col-md-6 -->

    </div>

    <!-- /.row -->

</div>

<!-- /.container-fluid -->

@endsection

@push('scripts')

<script>
    $(document).ready(function() {

        bsCustomFileInput.init();

        function toggleEditMode(isEditMode) {

            $('#toggleButton')
                .removeClass(isEditMode ? 'btn-secondary' : 'btn-primary')
                .addClass(isEditMode ? 'btn-primary' : 'btn-secondary')
                .text(isEditMode ? 'Simpan Perubahan' : 'Klik untuk Ubah Profil')
                .attr('type', isEditMode ? 'submit' : 'button');

            $('#batal').toggleClass('d-none', !isEditMode);

            $('#nama, #alamat').prop('readonly', !isEditMode);

            $('#nama').focus();

        }

        $('#toggleButton').click(function(e) {

            e.preventDefault();

            if ($(this).hasClass('btn-secondary')) {

                toggleEditMode(true);

            } else {

                toggleEditMode(false);

                $('#profilForm').submit();

            }

        });

        $('#batal').click(function() {

            toggleEditMode(false);

            // Kembalikan nilai input ke nilai awal dari database

            $('#nama, #alamat').each(function() {

                $(this)
                    .val($(this).data('initial-value'))
                    .prop('readonly', true);

            });

            // Kembalikan gambar ke gambar awal
            $('#image').val('');

            $('.img-preview').attr('src', "{{ asset('storage/' . Auth::user()->image) }}");

            $('.custom-file-label').html('<i class="fas fa-upload" aria-hidden="true"></i><span class="sr-only">Pilih file...</span>');

        });

    });

    function previewImage() {

        const image = $('#image');

        const imgPreview = $('.img-preview');

        const file = image[0].files[0];

        const reader = new FileReader();

        reader.onload = function(event) {

            imgPreview.attr('src', event.target.result);

        }

        if (file) {

            reader.readAsDataURL(file);

        }

    }
</script>

@endpush