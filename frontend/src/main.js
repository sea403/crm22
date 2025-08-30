import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import Toast from "vue-toastification";
import { createI18n } from "vue-i18n";
import '@vueup/vue-quill/dist/vue-quill.snow.css' // or .bubble.css for bubble theme

import en from "./locales/en.json";
import hi from "./locales/hi.json";
import hr from "./locales/hr.json";
import hu from "./locales/hu.json";
import uk from "./locales/uk.json";
import ta from "./locales/ta.json";
import ab from "./locales/ab.json";
import af from "./locales/af.json";
import as from "./locales/as.json";
import ba from "./locales/ba.json";
import az from "./locales/az.json";
import bg from "./locales/bg.json";
import bo from "./locales/bo.json";

import aa from "./locales/aa.json";
import ak from "./locales/ak.json";
import am from "./locales/am.json";
import an from "./locales/an.json";
import ar from "./locales/ar.json";
import av from "./locales/av.json";
import ay from "./locales/ay.json";
import be from "./locales/be.json";
import bh from "./locales/bh.json";
import bi from "./locales/bi.json";
import bm from "./locales/bm.json";
import bn from "./locales/bn.json";
import br from "./locales/br.json";
import bs from "./locales/bs.json";
import ca from "./locales/ca.json";
import ce from "./locales/ce.json";
import ch from "./locales/ch.json";
import co from "./locales/co.json";
import cr from "./locales/cr.json";
import cs from "./locales/cs.json";
import cu from "./locales/cu.json";
import cv from "./locales/cv.json";
import cy from "./locales/cy.json";
import da from "./locales/da.json";
import de from "./locales/de.json";
import dv from "./locales/dv.json";
import dz from "./locales/dz.json";
import ee from "./locales/ee.json";
import eo from "./locales/eo.json";
import es from "./locales/es.json";
import et from "./locales/et.json";
import eu from "./locales/eu.json";
import fa from "./locales/fa.json";
import ff from "./locales/ff.json";
import fi from "./locales/fi.json";
import fj from "./locales/fj.json";
import fo from "./locales/fo.json";

const messages = {
  ab,
  af,
  as,
  ba,
  az,
  bg,
  bo,
  en,
  hi,
  hr,
  hu,
  uk,
  ta,
  aa,
  ak,
  am,
  an,
  ar,
  av,
  ay,
  be,
  bh,
  bi,
  bm,
  bn,
  br,
  bs,
  ca,
  ce,
  ch,
  co,
  cr,
  cs,
  cu,
  cv,
  cy,
  da,
  de,
  dv,
  dz,
  ee,
  eo,
  es,
  et,
  eu,
  fa,
  ff,
  fi,
  fj,
  fo,
};

const defaultLanguage = localStorage.getItem("default_language") || "en"; // You can sync this later from API

const i18n = createI18n({
  locale: defaultLanguage,
  fallbackLocale: "en",
  messages,
});

import "vue-toastification/dist/index.css";
import "./assets/styles/main.less";
import { createPinia } from "pinia";
import { QuillEditor } from "@vueup/vue-quill";

const pinia = createPinia()

const app = createApp(App);

app.component('QuillEditor', QuillEditor)

app.use(pinia);

app.use(router);

app.use(Toast);

app.use(i18n);

app.mount("#app");
