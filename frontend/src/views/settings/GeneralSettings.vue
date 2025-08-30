<template>
  <div class="page-header">
    <div>
      <h2>{{ $t('generalSettings') }}</h2>
      <p>{{ $t('systemGeneralSetting') }}</p>
    </div>

    <BaseButton @click="handleSave" :disabled="loading">
      <template v-if="loading">Saving...</template>
      <template v-else>{{ $t('save') }}</template>
    </BaseButton>
  </div>

  <div class="page-content">
    <!-- Regional Settings -->
    <section>
      <div class="page-content-section-header">
        <p>Regional Settings</p>
      </div>
      <div class="page-content-section-content">
        <table>
          <tr>
            <td><label class="text-12">{{ $t('defaultCurrency') }}</label></td>
            <td>
              <BaseSelect v-model="form.default_currency" size="sm" class="base-select-sm">
                <option value="" disabled>Select Default Currency</option>
                <option value="USD">USD</option>
                <option value="BDT">BDT</option>
                <option value="IDR">IDR</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('currencyFormat') }}</label></td>
            <td>
              <BaseSelect v-model="form.currency_format" size="sm" class="base-select-sm">
                <option value="" disabled>Select Currency Format</option>
                <option value="$10 USD">$10 USD</option>
                <option value="10 USD">10 USD</option>
                <option value="$10">$10</option>
                <option value="10 $">10 $</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('numberFormat') }}</label></td>
            <td>
              <BaseSelect v-model="form.number_format" size="sm" class="base-select-sm">
                <option value="" disabled>Select Format</option>
                <option value="1,234.56">1,234.56</option>
                <option value="1.234,56">1.234,56</option>
                <option value="1234.56">1234.56</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('defaultCountry') }}</label></td>
            <td>
              <BaseSelect v-model="form.default_country" size="sm" class="base-select-sm">
                <option value="" disabled>Select Country</option>
                <option v-for="country in countries" :key="country.code" :value="country.code">
                  {{ country.name }}
                </option>
              </BaseSelect>
            </td>
          </tr>
        </table>
      </div>
    </section>

    <!-- Date & Time Settings -->
    <section>
      <div class="page-content-section-header">
        <p>{{ $t('dateTimeSettings') }}</p>
      </div>
      <div class="page-content-section-content">
        <table>
          <tr>
            <td><label class="text-12">{{ $t('timezone') }}</label></td>
            <td>
              <BaseSelect v-model="form.timezone" size="sm" class="base-select-sm">
                <option value="" disabled>Select Timezone</option>
                <option v-for="timezone in timezones" :key="timezone.value" :value="timezone.value">
                  {{ timezone.text }}
                </option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('startDayOfWeek') }}</label></td>
            <td>
              <BaseSelect v-model="form.start_day_of_week" size="sm" class="base-select-sm">
                <option value="" disabled>Select Day</option>
                <option v-for="day in days" :key="day" :value="day">{{ day }}</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('dateFormat') }}</label></td>
            <td>
              <BaseSelect v-model="form.date_format" size="sm" class="base-select-sm">
                <option value="" disabled>Select Date Format</option>
                <option v-for="df in dateFormats" :key="df" :value="df">{{ df }}</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('timeFormat') }}</label></td>
            <td>
              <BaseSelect v-model="form.time_format" size="sm" class="base-select-sm">
                <option value="" disabled>Select Time Format</option>
                <option value="12">12-hour (e.g., 3:45 PM)</option>
                <option value="24">24-hour (e.g., 15:45)</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('fiscalYearStart') }}</label></td>
            <td>
              <BaseSelect v-model="form.fiscal_year_start" size="sm" class="base-select-sm">
                <option value="" disabled>Select Month</option>
                <option v-for="month in months" :key="month" :value="month">{{ month }}</option>
              </BaseSelect>
            </td>
          </tr>
        </table>
      </div>
    </section>

    <!-- Localization -->
    <section>
      <div class="page-content-section-header">
        <p>{{ $t('localization') }}</p>
      </div>
      <div class="page-content-section-content">
        <table>
          <tr>
            <td><label class="text-12">{{ $t('defaultLanguage') }}</label></td>
            <td>
              <BaseSelect v-model="form.default_language" size="sm" class="base-select-sm">
                <option value="" disabled>Select Language</option>
                <option v-for="language in languages" :value="language.code">{{ language.name }}</option>
              </BaseSelect>
            </td>
          </tr>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import BaseSelect from '@/components/base/BaseSelect.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import timezones from '@/data/timezones.json'
import countries from '@/data/countries.json'
import languages from '@/data/languages.json'
import { getSettings, updateSettings } from '@/services/settings'
import { useToast } from "vue-toastification";
const toast = useToast();
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const dateFormats = ['DD/MM/YYYY', 'YYYY/DD/MM', 'DD-MM-YYYY']

const form = ref({
  default_currency: '',
  currency_format: '',
  number_format: '',
  default_country: '',
  timezone: '',
  start_day_of_week: '',
  date_format: '',
  time_format: '',
  fiscal_year_start: '',
  default_language: ''
});

watch(() => form.value.default_language, (newLang) => {
  if (newLang) {
    locale.value = newLang
    localStorage.setItem('default_language', newLang)
  }
})

const loading = ref(false)

onMounted(async () => {
  const settings = await getSettings()
  if (settings) Object.assign(form.value, settings)
})

const saving = ref(false)

const handleSave = async () => {
  if (saving.value) return // 👈 prevent double execution
  saving.value = true
  loading.value = true
  try {
    await updateSettings(form.value)
    toast.success("Settings updated successfully!");
  } catch (e) {
    alert('Failed to save settings')
  } finally {
    loading.value = false
    saving.value = false
  }
}
</script>

<style lang="less" scoped>
.page-header {
  color: #484848;
  border-bottom: 1px solid #f2f2f2;
  padding-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 44px;
  background-color: #ffffff;

  h2 {
    margin: 0;
  }

  p {
    margin: 0;
  }
}

.page-content {
  &-section-header {
    background-color: #f0f0f0;
    padding: 5px 10px;

    p {
      margin: 0;
    }
  }

  &-section-content {
    padding: 1rem;
    border-left: 1px solid #f0f0f0;

    table {
      border-spacing: 0 10px;
      border-collapse: separate;
    }

    td {
      padding: 5px 10px;
    }
  }
}
</style>