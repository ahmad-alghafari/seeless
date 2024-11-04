<template>
    <div class="row flex-center min-vh-100 py-6 text-center">
          <div class="col-sm-10 col-md-8 col-lg-6 col-xxl-5">
            <router-link class="d-flex flex-center mb-4" to="/dashboard">
                <img class="me-2" :src="`/assets/img/icons/spot-illustrations/falcon.png`" alt="" width="58" />
                <span class="font-sans-serif fw-bolder fs-5 d-inline-block">{{ $t('seless') }}</span></router-link>
            <div class="card">
              <div class="card-body p-4 p-sm-5">
                <div class="fw-black lh-1 text-300 fs-error">404</div>
                <p class="lead mt-4 text-800 font-sans-serif fw-semi-bold w-md-75 w-xl-100 mx-auto">{{ $t("404_alert") }}</p>
                <hr />
                <p>{{ $t('404_explane') }} <a href="mailto:info@exmaple.com">{{ $t("contact_us") }}</a>.</p><router-link class="btn btn-primary btn-sm mt-3" to="/dashboard"><span class="fas fa-home me-2"></span>{{ $t("404_back_home") }}</router-link>
              </div>
            </div>
          </div>
        </div>
</template>