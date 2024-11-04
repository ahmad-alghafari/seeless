<script setup>

import { onMounted, ref, onBeforeMount } from "vue";
import axios, { Axios } from "axios";


import { useRouter } from "vue-router";
const router = useRouter();

import { useToast } from 'vue-toastification';
const toast = useToast();

import { useStore } from 'vuex';
const store = useStore();

const isLoading = ref();
const time = ref(5000);
const table_id = ref();
const QRcodes = ref({});

onBeforeMount(() => {
    if (!store.state.isLoggedIn) {
        router.push({ name: "login" });
        store.dispatch('logout');
    }
});

onMounted(() => {
    fetching();
});

const fetching = () => {
    console.log("fetching start");
    isLoading.value = true;
    axios.get("http://127.0.0.1:8000/api/QRcode",
        {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + store.state.token
            },
        }).then(
            function (response) {
                if (response.status == 200) {
                    QRcodes.value = response.data.QRcodes;
                }
            }
        ).catch(function (error) {
            if (error.response && error.response.status === 401) {
                console.log("Unauthorized - logging out.");
                store.dispatch('logout');
                router.push({ name: "login" });
            }
            toast.error('حدث خطأ ما', { timeout: time });
            console.log("catch error : " + error);
        }).finally(function () {
            isLoading.value = false;
            console.log("fetching end");
        });
}

const submit = () => {
    console.log("submit start");
    axios.post("http://127.0.0.1:8000/api/QRcode/generate",
        {
            id: table_id.value
        }, {
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + store.state.token
        },
    }).then(
        function (response) {
            if (response.status == 200) {
                QRcodes.value[response.data.QRcode.id] = response.data.QRcode;
                toast.success("added successfully", { timeout: time.value });
            }
        }
    ).catch(function (error) {
        if (error.response && error.response.status === 401) {
            console.log("Unauthorized - logging out.");
            store.dispatch('logout');
            router.push({ name: "login" });
        }
        toast.error('حدث خطأ ما', { timeout: time.value });
        console.log("catch error : " + error);
    }).finally(function () {
        console.log("submit end");
    });
}

const delete_ = (code) => {
    console.log("delete start");
    axios.delete("http://127.0.0.1:8000/api/QRcode/delete/" + code ,
        {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + store.state.token
            },
        }).then(
            function (response) {
                if (response.status == 200) {
                    delete QRcodes.value[code];
                    toast.success("deleted successfully", { timeout: time.value });
                }
            }
        ).catch(function (error) {
            if (error.response && error.response.status === 401) {
                console.log("Unauthorized - logging out.");
                store.dispatch('logout');
                router.push({ name: "login" });
            }
            toast.error('حدث خطأ ما', { timeout: time.value });
            console.log("catch error : " + error);
        }).finally(function () {
            console.log("delete end");
        });
}


const print = (id) => {
    const printContents = document.getElementById(id).outerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;

    window.location.reload();
}

const since = (date) => {
  const currentDate = new Date();
  const pastDate = new Date(date);
  const diffInSeconds = Math.floor((currentDate - pastDate) / 1000);
  let interval = Math.floor(diffInSeconds / 31536000);
  if (interval > 1) {
    return `منذ ${interval} سنة`;
  }

  interval = Math.floor(diffInSeconds / 2592000);
  if (interval > 1) {
    return `منذ ${interval} شهر`;
  }

  interval = Math.floor(diffInSeconds / 86400);
  if (interval > 1) {
    return `منذ ${interval} يوم`;
  }

  interval = Math.floor(diffInSeconds / 3600);
  if (interval > 1) {
    return `منذ ${interval} ساعة`;
  }

  interval = Math.floor(diffInSeconds / 60);
  if (interval > 1) {
    return `منذ ${interval} دقيقة`;
  }

  return `منذ ${diffInSeconds} ثانية`;
};

</script>
<template>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" data-anchor="data-anchor">{{ $t('add_category') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body bg-light">
                <div class="tab-content">
                    <div class="tab-pane preview-tab-pane active" role="tabpanel"
                        aria-labelledby="tab-dom-160a4566-7e94-45a2-bf04-b36ef49d954f"
                        id="dom-160a4566-7e94-45a2-bf04-b36ef49d954f">
                        <form>
                            <div class="mb-3">
                                <label class="form-label" for="basic-form-name">{{ $t('name') }}</label>
                                <input class="form-control" id="basic-form-name" v-model="table_id" type="number"
                                    placeholder="1" />
                            </div>
                            <button class="btn btn-primary" type="button" @click="submit">{{ $t('add') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row light">
        <div class="col-sm-6 col-lg-4 mb-4" v-for="code in Object.keys(QRcodes) " :key="code">
            <div class="card overflow-hidden" style="width: 22rem;">
                <div class="card-img-top"><img :id="code" class="img-fluid" :src="`/` + QRcodes[code].path"
                        alt="Card image cap" />
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $t('table_number') }} : {{ QRcodes[code].table_number }}</h5>
                    <p class="card-text">
                        {{ since(QRcodes[code].updated_at) }}
                    </p>
                    <hr>
                    <div class="d-flex flex-between-center px-5">
                        <div>
                            <button class="btn btn-sm btn-falcon-default me-2" type="button" @click="print(code)">{{
                                $t('print') }}</button>
                        </div>
                        <div>
                            <button class="btn btn-danger btn-sm me-2" type="button" @click="delete_(code)">{{
                                $t('delete')
                                }}
                            </button>
                        </div>
                        <div>
                            <a class="btn btn-sm btn-falcon-default me-2" :href="`/` + QRcodes[code].path"
                                download="qrcode.png">
                                {{ $t('download') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>