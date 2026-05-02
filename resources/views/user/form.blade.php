<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="name" class="col-lg-3 col-lg-offset-1 control-label">Nama</label>
                        <div class="col-lg-6">
                            <input type="text" name="name" id="name" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_agen" class="col-lg-3 col-lg-offset-1 control-label">Akses sebagai Agen</label>
                        <div class="col-lg-6">
                            <select name="id_agen" id="id_agen" class="form-control">
                                <option value="">-- Pilih Agen --</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id_member }}">{{ $agent->kode_member }} - {{ $agent->nama }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_agen" id="is_agen" value="1"> User ini adalah agen
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses_outlet" class="col-lg-3 col-lg-offset-1 control-label">Akses Outlet</label>
                        <div class="col-lg-6">
                            @foreach($outlets as $outlet)
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="akses_outlet[]" value="{{ $outlet->id_outlet }}" class="akses-outlet-checkbox"> {{ $outlet->nama_outlet }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses_khusus" class="col-lg-3 col-lg-offset-1 control-label">Akses Khusus</label>
                        <div class="col-lg-6">
                            @foreach($aksesKhususOptions as $kh)
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="akses_khusus[]" value="{{ $kh }}" class="akses-khusus-checkbox"> {{ ucfirst($kh) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses" class="col-lg-3 col-lg-offset-1 control-label">Hak Akses</label>
                        <div class="col-lg-6">
                            <div class="row equal-height">
                                @foreach($modules as $module)
                                <div class="col-md-4 d-flex">
                                    <div class="panel panel-default flex-fill">
                                        <div class="panel-heading">
                                            <label>
                                                <input type="checkbox" name="akses[]" value="{{ $module }}" class="akses-checkbox" data-module="{{ $module }}"> 
                                                <strong>{{ $module }}</strong>
                                            </label>
                                        </div>
                                        <div class="panel-body">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="{{ $module }} View" class="akses-checkbox {{ $module }}-checkbox"> View
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="{{ $module }} Create" class="akses-checkbox {{ $module }}-checkbox"> Create
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="{{ $module }} Edit" class="akses-checkbox {{ $module }}-checkbox"> Edit
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="{{ $module }} Delete" class="akses-checkbox {{ $module }}-checkbox"> Delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="email" class="col-lg-3 col-lg-offset-1 control-label">Email</label>
                        <div class="col-lg-6">
                            <input type="email" name="email" id="email" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-lg-3 col-lg-offset-1 control-label">Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password" id="password" class="form-control" 
                            required
                            minlength="6">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-lg-3 col-lg-offset-1 control-label">Konfirmasi Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                required
                                data-match="#password">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
