
<div class="col mb-lg-0 mb-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col">
            <div class="d-flex flex-column h-100">
                <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto" href="{{ route('user_management_admin') }}" >
                    <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
                      Kembali
                    </a>
                <h5 class="font-weight-bolder">{{ $position }}</h5>


                <form id="form-data" method="post" autocompleted="off" enctype="multipart/form-data">
                    {{csrf_field();}}
                    <div class="form-group row">
                        <div class="col-6 mt1">
                            <label for="name">Nama </label>
                            <input class="form-control input_form" type="text" name="name" id="" value="{{ isset($data_user) ? $data_user->name : ''}}" required>
                        </div>
                
                        <div class="col-6 mt1">
                            <label for="email">Email </label>
                            <input class="form-control input_form" type="email" name="email" id="" value="{{ isset($data_user) ? $data_user->email : ''}}" required>
                        </div>

                        <div class="col-6 mt1">
                          <label for="no_telpon">No Telpon</label>
                          <input class="form-control input_form" type="number" name="no_telpon"  value="{{ isset($data_user) ? $data_user->no_telpon : ''}}" required>
                        </div>
                
                        <div class="col-6 mt1">
                            <label for="password">Password</label>
                            <input class="form-control input_form" type="password" name="password"  value="">
                        </div>
                
                        <div class="col mt1">
                            <label for="status_role">Status Pengguna</label>
                            <select  class="form-control input_form" name="status_role" id="status_role" required >
                                <option value="0" {{ isset($data_user) ? ($data_user->status_role == 0 ? 'selected' : '' ) : '' }}>Admin</option>
                                <option value="1" {{ isset($data_user) ? ($data_user->status_role == 1 ? 'selected' : '' ) : '' }}>Dokter</option>
                                <option value="2" {{ isset($data_user) ? ($data_user->status_role == 2 ? 'selected' : '' ) : '' }}>Pengguna Biasa</option>
                            </select>
                        </div>

                        <div id="new_c">
                          {{-- <textarea name="" id="" cols="30" rows="10"></textarea> --}}
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


