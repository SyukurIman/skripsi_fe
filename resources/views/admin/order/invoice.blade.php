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
                            <label for="nama_paket">Harga</label>
                            <input class="form-control input_form" type="text" name="nama_paket" disabled value="{{$type == 'update' ? $data_order->harga : ''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-select select id_kategori_layanan">
                              <option value="" readonly>pilih....</option>
                              <option value="0" {{$type == 'update' ? ($data_order->status_pembayaran == 0 ?'selected':'') :''}} >Menunggu Pembayaran</option>
                              <option value="1" {{$type == 'update' ? ($data_order->status_pembayaran == 1 ?'selected':'') :''}} >Sudah Terbayarkan</option>
                              <option value="2" {{$type == 'update' ? ($data_order->status_pembayaran == 2 ?'selected':'') :''}} >Dibatalkan</option>
                              <option value="3" {{$type == 'update' ? ($data_order->status_pembayaran == 3 ?'selected':'') :''}} >Kadarluarsa</option>
                            </select>
                        </div>
                        <div class="col-3 mt-2">
                            <label for="max_sesi">Email User</label>
                            <input disabled class="form-control input_form" type="text" name="email_user" value="{{$type == 'update' ? $data_order->user->email:''}}" required>
                        </div>
                        <div class="col-3 mt-2">
                            <label for="kadaluarsa">Nama User</label>
                            <input disabled class="form-control input_form" type="text" name="nama_user" value="{{$type == 'update' ? $data_order->user->name : ''}}" required>
                        </div>

                        <div class="col-6 mt-2">
                            <label for="max_sesi">Tanggal Kadarluarsa</label>
                            <input class="form-control input_form" type="text" name="tanggal_kadaluarsa" value="{{$type == 'update' ? $data_order->tanggal_kadaluarsa:''}}" required>
                        </div>

                    </div>

                    <div class="form-group row px-2">
                        <div class="col-6 mt-2">
                            <label for="nama_paket">Nama Paket</label>
                            <input  type="hidden" name="id_paket" value="{{$type == 'update' ? $data_order->paket->id:''}}">
                            <input class="form-control input_form" type="text" name="nama_paket" disabled value="{{$type == 'update' ? $data_order->paket->nama_paket : ''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="id_kategori_layanan">Kategori Layanan</label>
                            <select disabled name="id_kategori_layanan" id="id_kategori_layanan" class="form-select select id_kategori_layanan">
                              <option value="" readonly>pilih....</option>
                              @foreach ($kategori_layanan as $kategori)
                              <option value="{{$kategori->id}}" {{$type == 'update' ? ($data_order->paket->id_kategori_layanan == $kategori->id ?'selected':'') :''}} >{{$kategori->nama}}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-3 mt-2">
                          <label for="max_sesi">Maksimal Sesi </label>
                          <input disabled class="form-control input_form" type="number" name="max_sesi" value="{{$type == 'update' ? $data_order->paket->max_sesi:''}}" required>
                        </div>
                        <div class="col-3 mt-2">
                          <label for="kadaluarsa">Masa Aktif</label>
                          <input disabled class="form-control input_form" type="number" min="1" max="12" name="kadaluarsa" value="{{$type == 'update' ? $data_order->paket->kadaluarsa:''}}" required>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="harga">harga</label>
                          <input disabled class="form-control input_form" type="text" id="harga" name="harga" value="{{$type == 'update' ? rupiah($data_order->paket->harga):''}}" required>
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
                            </tr>
                          </thead>
                           </thead>
                           <tbody>
                            @forEach($data_order->paket->detail_paket as $detail)
                            <tr class="text-center">
                              <td >{{$loop->index + 1}}</td>
                              <input disabled type="hidden" name="id_detail_paket[]" value="{{$detail->id}}" required>
                              <td><input disabled class="form-control input_form deskripsi_paket" type="text" name="deskripsi_paket[]" value="{{$detail->deskripsi_paket}}" required></td>
                            </tr>
                            @endforEach
                           </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary" id="simpan">
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


