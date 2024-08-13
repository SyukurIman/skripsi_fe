<div class="col mb-lg-0 mb-4 px-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col">
            <div class="d-flex flex-column h-100">
                <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto mb-4" href="{{ route('paket.index') }}" >
                    <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
                      Kembali
                    </a>
                <h5 class="font-weight-bolder">{{ $position }}</h5>


                <form id="form-data" method="post" autocompleted="off" enctype="multipart/form-data">
                    {{csrf_field();}}
                    <div class="form-group row px-2">
                        <div class="col-6 mt-2">
                            <label for="nama_paket">Nama Paket</label>
                            <input  type="hidden" name="id" value="{{$type == 'update' ? $data_paket->id:''}}">
                            <input class="form-control input_form" type="text" name="nama_paket" value="{{$type == 'update' ? $data_paket->nama_paket:''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="id_kategori_layanan">Kategori Layanan</label>
                            <select name="id_kategori_layanan" id="id_kategori_layanan" class="form-select select id_kategori_layanan">
                              <option value="" readonly>pilih....</option>
                              @foreach ($kategori_layanan as $kategori)
                              <option value="{{$kategori->id}}" {{$type == 'update' ? ($data_paket->id_kategori_layanan == $kategori->id ?'selected':'') :''}} >{{$kategori->nama}}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-3 mt-2">
                          <label for="max_sesi">Maksimal Sesi </label>
                          <input class="form-control input_form" type="number" name="max_sesi" value="{{$type == 'update' ? $data_paket->max_sesi:''}}" required>
                        </div>
                        <div class="col-3 mt-2">
                          <label for="kadaluarsa">Masa Aktif</label>
                          <input id="kadaluarsa" class="form-control input_form" type="number" min="1" max="12" name="kadaluarsa" value="{{$type == 'update' ? $data_paket->kadaluarsa:''}}" required>
                        </div>
                        <div class="col-3 mt-2">
                            <label for="rentang_pengalaman_min">rentang minimal</label>
                            <input class="form-control input_form" type="number" name="rentang_pengalaman_min" value="{{$type == 'update' ? $data_paket->rentang_pengalaman_min:''}}">
                          </div>
                        <div class="col-3 mt-2">
                            <label for="rentang_pengalaman_max">rentang maksimal</label>
                            <input class="form-control input_form" type="number" name="rentang_pengalaman_max" value="{{$type == 'update' ? $data_paket->rentang_pengalaman_max:''}}">
                        </div>
                        <div class="col-6 mt-2">
                            <label for="fitur">Fitur</label>
                            <input class="form-control input_form" type="text" id="fitur" name="fitur" value="{{$type == 'update' ? $data_paket->fitur :''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="max_durasi">Durasi</label>
                            <input class="form-control input_form" type="text" id="max_durasi" name="max_durasi" value="{{$type == 'update' ? $data_paket->max_durasi :''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="harga_persesi">harga persesi</label>
                            <input class="form-control input_form" type="text" id="harga_persesi" name="harga_persesi" value="{{$type == 'update' ? rupiah($data_paket->harga_persesi):''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="harga">total</label>
                          <input class="form-control input_form" type="text" id="harga" name="harga" value="{{$type == 'update' ? rupiah($data_paket->harga):''}}" required>
                        </div>
                        <div class="col-12 mt-3 text-start">
                          <button type="button" class="btn btn-primary btn-raised btn-xs" id="add_row">+ ADD Detail</button>
                        </div>
                        <div class="col-12">
                          <table class="table table-hover table_detail">
                           <thead class="text-center">
                            <tr>
                              <th class="text-uppercase text-xs font-weight-bolder" style="width: 5px;">
                                  No
                              </th>
                              <th class="text-uppercase text-xs font-weight-bolder">
                                Detail paket
                              </th>
                              <th class="text-uppercase text-xs font-weight-bolder">
                                Aksi
                            </th>
                            </tr>
                          </thead>
                           </thead>
                           <tbody>
                            @if ($type == 'update')
                            @forEach($data_detail_paket as $detail)
                            <tr class="text-center">
                              <td >{{$loop->index + 1}}</td>
                              <input type="hidden" name="id_detail_paket[]" value="{{$detail->id}}" required>
                              <td><input class="form-control input_form deskripsi_paket" type="text" name="deskripsi_paket[]" value="{{$detail->deskripsi_paket}}" required></td>
                              <td ><button type="button" class="btn btn-danger btn-raised btn-xs btn-hapus-detail" title="Hapus"><i class="icon-trash"></i></button></td>
                            </tr>
                            @endforEach
                            @if($data_detail_paket->count() == 0)
                            <tr>
                              <td colspan="99" class="text-center">Klik ADD detail</td>
                            </tr>
                            @endif
                            @else
                            <tr>
                              <td colspan="99" class="text-center">Klik ADD detail</td>
                            </tr>
                            @endif
                           </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="text-end">
                      <button type="button" class="btn btn-primary" id="simpan">
                        SUBMIT
                      </button>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


