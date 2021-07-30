<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RajaOngkir</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <div id="app" class="container mt-3">
    <div class="card" x-data="app()" x-init="init()">
      <div class="card-header">
        <h5 class="m-0">Cek Ongkos Kirim - RajaOngkir</h5>
      </div>
      <div class="card-body">
        <form action="#" id="form" class="row mb-3" @submit.prevent="handleSubmit">
          <div class="col-12 col-md-4 mb-3">
            <label for="origin" class="form-label">Asal Kota</label>
            <input type="text" class="form-control" id="origin" value="Jogja" disabled>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <label for="province_list" class="form-label">Provinsi Tujuan</label>
            <input class="form-control" list="province_list_opt" id="province_list" placeholder="Ketik untuk mencari..."
              x-model.debounce="data.provincies_selected">
            <datalist id="province_list_opt">
              <template x-if="data.provincies.length > 0">
                <template x-for="province in data.provincies">
                  <option :value="province.province"></option>
                </template>
              </template>
            </datalist>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <label for="city" class="form-label">Kabupaten Tujuan</label>
            <input class="form-control" list="city_opt" id="city" placeholder="Ketik untuk mencari..."
              x-model.debounce="data.cities_selected">
            <datalist id="city_opt">
              <template x-if="data.cities.length > 0">
                <template x-for="city in data.cities">
                  <option :value="city.city_name"></option>
                </template>
              </template>
            </datalist>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <label for="weight" class="form-label">Berat</label>
            <div class="input-group">
              <input class="form-control" id="weight" placeholder="Masukkan berat barang.." x-model="form.weight">
              <span class="input-group-text">gram</span>
            </div>
          </div>

          <div class="col-12 col-md-8 mb-3">
            <label for="courier" class="form-label">Pilih Kurir</label>
            <div class="d-flex">
              <template x-for="courier in data.couriers">
                <button type="button" class="btn btn-sm me-1" :data-code="courier.code"
                  :class="{'btn-danger': (form.courier == courier.code), 'btn-outline-danger': (form.courier != courier.code)}"
                  @click="form.courier = courier.code" x-text="courier.name"></button>
              </template>
            </div>
          </div>

          <div class="col-12 mb-3">
            <button class="btn btn-primary" type="submit">Cek Ongkir</button>
          </div>

          <!-- loading state -->
          <template x-if="state.loading">
            <div class="mb-3">
              <div class="text-center text-primary">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
          </template>
        </form>

        <!-- warning -->
        <template x-if="state.error">
          <div class="alert alert-warning mb-3">
            <p>
              <strong>Peringatan!</strong>
              <span x-text="state.error"></span>
            </p>
          </div>
        </template>

        <!-- results -->
        <template x-if="results.length > 0">
          <div>
            <hr>
            <h3>Hasil Pengecekan</h3>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Kurir</th>
                    <th scope="col">Jenis Layanan</th>
                    <th scope="col">Waktu Pengiriman</th>
                    <th scope="col">Tarif</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="result in results">
                    <tr>
                      <td>
                        <h5 x-text="result.code"></h5>
                        <h6 class="text-muted" x-text="result.name"></h6>
                      </td>
                      <td>
                        <h5 x-text="result.service"></h5>
                        <h6 class="text-muted" x-text="result.description"></h6>
                      </td>
                      <td x-text="formatDate(result.cost.etd)"></td>
                      <td x-text="formatCurrency(result.cost.value)"></td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.js" defer></script>
<script>
  const URL_PROVINCE = "{{ route('province') }}";
  const URL_CITY = "{{ route('city') }}";
  const URL_COST = "{{ route('cost') }}";

  const app = () => {
    return {
      init() {
        // watch are province is change
        this.$watch('data.provincies_selected', (value) => {
          // if value is not emty
          if (value) {
            // validate province input
            const provincies = this.data.provincies.filter(el => el.province == value);
            if (provincies.length > 0) {
              // get cities by given province id
              this.getCities(provincies[0].province_id)
            }
          } else {
            // get cities
            this.getCities();
          }
        }
        );

        // watch are city is change
        this.$watch('data.cities_selected', (value) => {
          if (value) {
            // validate province input
            const cities = this.data.cities.filter(el => el.city_name == value);
            if (cities.length > 0) {
              // append city id to form
              this.form.destination = cities[0].city_id;
            } else {
              this.form.destination = '';
            }
          }
        });

        // get provincies
        this.getProvincies();
        // get cities
        this.getCities();
      },
      state: {
        loading: false,
        error: ''
      },
      data: {
        provincies: [],
        provincies_selected: '',
        cities: [],
        cities_selected: '',
        // courier on starter account
        couriers: [
          {
            name: 'JNE',
            code: 'jne'
          },
          {
            name: 'POS Indonesia',
            code: 'pos'
          },
          {
            name: 'TIKI',
            code: 'tiki'
          }
        ]
      },
      form: {
        origin: 135, // jogja
        destination: '',
        weight: '',
        courier: 'jne',
      },
      results: [],
      async getProvincies() {
        return fetch(URL_PROVINCE)
          .then(r => r.json())
          .then(json => {
            if (json.ok && json.rajaongkir?.status?.code == 200) {
              this.data.provincies = json.rajaongkir.results;
            }
          });
      },
      async getCities(province_id = '') {
        return fetch(`${URL_CITY}${province_id != '' ? ('?province=' + province_id) : ''}`)
          .then(r => r.json())
          .then(json => {
            if (json.ok && json.rajaongkir?.status?.code == 200) {
              this.data.cities = json.rajaongkir.results;
            }
          });
      },
      async handleSubmit() {
        // show loading
        this.state.loading = true;

        // new url params
        const query = new URLSearchParams();
        // as query parameters
        Object.keys(this.form).map(el => query.append(el, this.form[el]));

        await fetch(`${URL_COST}?${query.toString()}`, { method: 'POST' })
          .then(r => r.json())
          .then(json => {
            // handle response

            // status valid from server
            if (json.ok) {
              const rajaongkir = json.rajaongkir;

              // response valid from api server
              if (rajaongkir.status.code == 200) {
                // append data
                if (rajaongkir.results.length > 0) {
                  const results = [];

                  rajaongkir.results.map(result => {
                    result.costs.map(cost => {
                      cost.cost.map(c => {
                        results.push({
                          code: result.code.toUpperCase(),
                          name: result.name,
                          service: cost.service,
                          description: cost.description,
                          cost: c
                        });
                      });
                    });
                  });

                  this.results = results;
                }
                // reset error
                this.state.error = '';
              } else {
                // response invalid from api server
                this.state.error = rajaongkir.status.description;
              }
            } else {
              this.state.error = json.msg;
            }

          })
          .catch(e => {
            this.state.error = "Kesalahan tidak diketahui";
          });

        // hide loading
        this.state.loading = false;
      },
      // format number to local currency (IDR)
      formatCurrency(number) {
        return 'Rp ' + (new Intl.NumberFormat('de-DE').format(number));
      },
      // format date (add prefix)
      formatDate(date) {
        return `${date} Hari`;
      }
    };
  }
</script>

</html>