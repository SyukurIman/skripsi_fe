<div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">
                  Total User
                </p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $total_user }}
                  <span class="text-success text-sm font-weight-bolder"
                    >User</span
                  >
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div
                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-money-coins text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">
                  Total Dokter
                </p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $total_dokter }}
                  <span class="text-success text-sm font-weight-bolder"
                    >Dokter</span
                  >
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div
                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-world text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">
                  Total Artikel
                </p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $total_artikel }}
                  <span class="text-danger text-sm font-weight-bolder"
                    >Artikel</span
                  >
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div
                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-paper-diploma text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">
                  Total Transaksi
                </p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $total_order }}
                  <span class="text-success text-sm font-weight-bolder"
                    >Transaksi</span
                  >
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div
                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md"
              >
                <i
                  class="ni ni-cart text-lg opacity-10"
                  aria-hidden="true"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-6">
              <div class="d-flex flex-column h-100">
                <p class="mb-1 pt-2 text-bold">Artikel Terbaru</p>
                <h5 class="font-weight-bolder">{{ isset($data_artikel) ? $data_artikel->judul : 'Judul Artikel'}}</h5>
                <p class="mb-5">
                    {{ isset($data_artikel) ? substr($data_artikel->deskripsi,0,200)."...." : 'Isi Artikel'}}
                </p>
                <a
                  class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                  href="/artikel/update/{{ isset($data_artikel) ? $data_artikel->id : 'no' }}"
                >
                  Read More
                  <i
                    class="fas fa-arrow-right text-sm ms-1"
                    aria-hidden="true"
                  ></i>
                </a>
              </div>
            </div>
            <div class="col-lg-5 ms-auto text-center mt-5 mt-lg-0">
              <div class="bg-gradient-primary border-radius-lg h-100" style="max-height: 250px">
                <img
                    class="w-100 position-relative z-index-2 pt-4"
                    src="{{ isset($data_artikel) ? '/storage/image/artikel/'.$data_artikel->link_gambar :'../assets/img/illustrations/rocket-white.png'}}"
                    alt="rocket" style="max-height: 250px"
                  />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card h-100 p-3">
        <div
          class="overflow-hidden position-relative border-radius-lg bg-cover h-100"
          style="background-image: url('../assets/img/ivancik.jpg')"
        >
          <span class="mask bg-gradient-dark"></span>
          <div
            class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3 pt-5"
          >
            <p class="fw-light text-bold text-white text-sm mb-0">Paket Terbaru</p>
            <h5 class="text-white font-weight-bolder mb-1">
                Paket {{ isset($data_paket) ? $data_paket->nama_paket : 'Layanan' }}
            </h5>
            <p class="text-white">
                Memiliki Fitur seperti
                @if(isset($data_paket) && $data_paket->detail_paket)
                @foreach($data_paket->detail_paket as $index => $detail)
                    {{ $index <= 3 ? $detail->deskripsi_paket.', ' : '' }}
                @endforeach
                dan sebagainya.
            @else
                <p>Layanan</p>
            @endif
            </p>
            <a
              class="text-white text-sm font-weight-bold mb-0 icon-move-right mt-auto"
              href="/paket/update/{{ isset($data_paket) ? $data_paket->id : 'no'  }}"
            >
              Read More
              <i
                class="fas fa-arrow-right text-sm ms-1"
                aria-hidden="true"
              ></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

