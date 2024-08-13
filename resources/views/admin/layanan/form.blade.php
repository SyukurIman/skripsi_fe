
<div class="col mb-lg-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col">
            <div class="d-flex flex-column h-100">
                <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto" href="{{ route('layanan_admin') }}" >
                    <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
                      Kembali
                    </a>
                <h5 class="font-weight-bolder">{{ $position }}</h5>


                <form id="form-data" method="post" autocompleted="off" enctype="multipart/form-data">
                    {{csrf_field();}}
                    <div class="form-group row">
                        <div class="col-6 mt1">
                            <label for="nama">Nama </label>
                            <input class="form-control input_form" type="text" name="nama" id="nama" value="{{ isset($data_layanan) ? $data_layanan->nama : ''}}" required>
                        </div>
                        <div class="col-6 mt1">
                          <label for="akses">Akses Layanan </label>
                          <select  class="form-control input_form" name="akses" id="akses" required >
                            <option value="psikolog"  {{ isset($data_layanan) ? ($data_layanan->akses == 'psikolog' ? 'selected' : '' ) : '' }}>Psikolog</option>
                            <option value="mentor"    {{ isset($data_layanan) ? ($data_layanan->akses == 'mentor'   ? 'selected' : '' ) : '' }}>Mentor</option>
                          </select>
                      </div>
                    </div>
                
                    <input class="btn btn-primary" type="submit" value="Submit">
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


