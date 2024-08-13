<div class="col mb-md-0 mb-4">
    <div class="card">
      <div class="card-header pb-0">
        <div class="row">
          <div class="col-lg-6 col-7">
            <h6>Data Paket</h6>
            <p class="text-sm mb-0">
              <i class="fa fa-check text-info" aria-hidden="true"></i>
              <span class="font-weight-bold ms-1">{{ $total_data }} Layanan </span> Secara
              Keseluruhan
            </p>
          </div>
          <div class="col-lg-6 col-5 my-auto text-end">
            <div class="dropdown float-lg-end pe-4">
                <a href="/paket/create" type="button" class="btn btn-primary btn-data" id="btn-create">
                    <i class="fa fa-plus-square"></i> <span>Tambah</span>
                </a>

                <button class="btn btn-secondary btn-data" id="filter_btn"><i class="fas fa-filter"></i> <span>Filter</span></button>
                <button class="btn btn-warning btn-data" id="download_btn"><i class="fas fa-download"></i> <span>Download</span></button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body m-3 px-2 p-2">
        <div class="table-responsive ">
          <table class="table align-items-center mb-0" id="table" style="width: 100%">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    No
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    Aksi
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                  Paket
              </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Layanan 
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  maksimal sesi 
                </th>    
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  masa berlaku 
                </th>      
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  harga 
                </th>      
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  isi paket 
                </th> 
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>