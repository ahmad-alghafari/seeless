<script setup>
import Navbar from "./navbar.vue";
import Settings from "./settings.vue";
import Foter from "./footer.vue";
import { ref, onMounted } from "vue";

import { useStore } from 'vuex';
const store = useStore();

const order = ref();
const name = ref();
const saved_orders = ref();

onMounted(() => {
  if (store.state.isLoggedIn) {
    order.value = store.state.order;
    name.value = store.state.name;
    saved_orders.value = store.state.saved_orders;

    const navbarElement = document.querySelector('.navbar-vertical');
    if (navbarElement) {
      const navbarStyle = localStorage.getItem("navbarStyle");
      if (navbarStyle && navbarStyle !== 'transparent') {
        navbarElement.classList.add(`navbar-${navbarStyle}`);
      }
    }
  }

  const isFluid = ref(JSON.parse(localStorage.getItem('isFluid')));
  if (isFluid) {
    const container = document.querySelector('[data-layout]');
    if (container) {
      container.classList.remove('container');
      container.classList.add('container-fluid');
    }
  }
});
</script>
<template>
  <!-- ===============================================-->
  <!--    Main Content-->
  <!-- ===============================================-->
  <main class="main" id="top">
    <div class="container" data-layout="container">
      <!-- <Burger/> -->
      <nav class="navbar navbar-light navbar-vertical navbar-expand-xl" v-if="store.state.isLoggedIn">


        <div class="d-flex align-items-center">
          <div class="toggle-icon-wrapper">
            <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip"
              data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span
                  class="toggle-line"></span></span></button>
          </div>
          <a class="navbar-brand" href="index.html">
            <div class="d-flex align-items-center py-3"><img class="me-2"
                :src="`/assets/img/icons/spot-illustrations/falcon.png`" alt="" width="40" /><span
                class="font-sans-serif">falcon</span>
            </div>
          </a>
        </div>

        <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
          <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

              <li class="nav-item">
                <!-- label-->
                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                  <div class="col-auto navbar-vertical-label">{{ $t('main') }}
                  </div>
                  <div class="col ps-0">
                    <hr class="mb-0 navbar-vertical-divider" />
                  </div>
                </div>


                <!-- parent pages-->
                <router-link class="nav-link" :to="{ name: 'main' }" role="button">
                  <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                        class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">
                      {{ $t('main') }}</span>
                  </div>
                </router-link>
              </li>


              <li class="nav-item">
                


                <!-- parent pages-->
                <router-link class="nav-link" :to="{ name: 'QRcode' }" role="button">
                  <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                        class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">
                      {{ $t('qr_code') }}</span>
                  </div>
                </router-link>
              </li>



              <li class="nav-item">
                <!-- parent pages--><a class="nav-link dropdown-indicator" href="#Food" role="button"
                  data-bs-toggle="collapse" aria-expanded="true" aria-controls="Food">
                  <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                        class="fas fa-chart-pie"></span></span><span class="nav-link-text ps-1">{{ $t('food') }}</span>
                  </div>
                </a>
                <ul class="nav collapse show" id="Food">

                  <!-- <li class="nav-item">
                    <router-link class="nav-link" :to="{ name: 'food' }">
                      <div class="d-flex align-items-center"><span class="nav-link-text ps-1">{{ $t('dishes') }}</span>
                      </div>
                    </router-link> -->
                    <!-- more inner pages-->
                  <li class="nav-item"><a class="nav-link dropdown-indicator" href="#level-two"
                      data-bs-toggle="collapse" aria-expanded="false" aria-controls="multi-level">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text ps-1">{{ $t('dishes') }}</span>
                      </div>
                    </a>
                    <!-- more inner pages-->
                    <ul class="nav collapse" id="level-two">
                      <li class="nav-item">
                        <router-link :to="{name : 'food'}" class="nav-link" >
                          <div class="d-flex align-items-center">
                            <span class="nav-link-text ps-1">{{ $t('show_list') }}</span>
                          </div>
                        </router-link>
                        <!-- more inner pages-->
                      </li>
                      <li class="nav-item">
                        <router-link :to="{name : 'addFood'}" class="nav-link" >
                          <div class="d-flex align-items-center">
                            <span class="nav-link-text ps-1">{{ $t('add_dishe') }}</span>
                          </div>
                        </router-link>
                        <!-- more inner pages-->
                      </li>
                    </ul>
                  </li>
              <!-- </li> -->

              <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'categories' }">
                  <div class="d-flex align-items-center"><span class="nav-link-text ps-1">{{ $t('categories')
                      }}</span>
                  </div>
                </router-link>
                <!-- more inner pages-->
              </li>
            </ul>
            </li>

            <li class="nav-item">
              <!-- parent pages--><a class="nav-link dropdown-indicator" href="#Orders" role="button"
                data-bs-toggle="collapse" aria-expanded="true" aria-controls="Orders">
                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                      class="fas fa-chart-pie"></span></span><span class="nav-link-text ps-1">{{ $t('orders')
                    }}</span>
                </div>
              </a>
              <ul class="nav collapse show" id="Orders">
                <li class="nav-item">
                  <router-link class="nav-link active" :to="{ name: 'orders' }">
                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">{{ $t('current_orders')
                        }}</span>
                    </div>
                  </router-link>
                  <!-- more inner pages-->
                </li>
                <li class="nav-item">
                  <router-link class="nav-link" :to="{ name: 'pastOrders' }">
                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">{{ $t('previous_orders')
                        }}</span>
                    </div>
                  </router-link>
                  <!-- more inner pages-->
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <!-- label-->
              <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                <div class="col-auto navbar-vertical-label">App
                </div>
                <div class="col ps-0">
                  <hr class="mb-0 navbar-vertical-divider" />
                </div>
              </div>
              <!-- parent pages--><a class="nav-link" href="app/calendar.html" role="button">
                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                      class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">Calendar</span>
                </div>
              </a>
            </li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="content">
        <Navbar v-if="store.state.isLoggedIn" />
        <!-- main vue view start -->
        <router-view />
        <!-- main vue view end -->
        <Foter v-if="store.state.isLoggedIn" />
      </div>
      <Settings v-if="store.state.isLoggedIn" />
    </div>
  </main>
  <!-- ===============================================-->
  <!--    End of Main Content-->
  <!-- ===============================================-->
</template>