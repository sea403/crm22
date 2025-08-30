<template>
    <div class="page">
        <div class="page-header">
            <div>
                <h1 class="page-header-title">Contact List</h1>
                <p class="page-header-subtitle">Mange your contacts for here</p>
            </div>

            <div class="d-flex gap-2">
                <BaseInput v-model="search" :hide_lebel="true" type="search" placeholder="Search here..." autocomplete="username" @input="debouncedFetch" />

                <BaseButton @click="openCreate">
                    <PlusIcon class="icon" />
                    <span>Add Contact</span>
                </BaseButton>
            </div>
        </div>

        <div class="contact-grid">
            <div v-for="contact in contacts" class="contact-grid-item" @click="openEdit(contact)">
                <div class="contact-grid-item-header">
                    <div>
                        <div class="no-image">
                            <p>{{ contact.name[0] }}</p>
                        </div>
                    </div>

                    <div>
                        <p>{{ contact.name }}</p>
                        <small>{{ contact.position ?? 'N/A' }}</small>
                        <small v-if="contact.account">Account: {{ contact.account.name }}</small>
                    </div>
                </div>

                <div class="contact-grid-item-content">
                    <p v-if="contact.email">
                        <EnvelopeIcon class="icon" />
                        {{ contact.email }}
                    </p>

                    <p v-if="contact.phone">
                        <PhoneIcon class="icon" />
                        {{ contact.phone }}
                    </p>

                    <p v-if="contact.company_name">
                        <BuildingOffice2Icon class="icon" />
                        {{ contact.company_name }}
                    </p>
                </div>
            </div>
        </div>

        <BaseOffCanvas :isOpen="showOffCanvas" :title="offcanvasTitle" @close="showOffCanvas = false">
            <div class="offcanvas-header">
                <BaseButton class="me-2" :disabled="loading" @click="submitForm">Save</BaseButton>
                <BaseButton variant="lightcoral" :disabled="loading" @click="submitForm">Save & New</BaseButton>
            </div>

            <BaseInput :error="errors.name" v-model="form.name" name="name" required class="mb-2" placeholder="Name" />
            <BaseInput :error="errors.email" v-model="form.email" name="email" required class="mb-2" type="email"
                placeholder="Email" />
            <BaseSelect v-model="form.account_id" name="account_id" class="mb-2">
                <option value="">Select Account (optional)</option>
                <option v-for="acc in accountOptions" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
            </BaseSelect>
            <BaseInput :error="errors.phone" v-model="form.phone" name="phone" class="mb-2" type="text"
                placeholder="Phone Number" />
            <BaseInput :error="errors.company_name" v-model="form.company_name" name="company_name" class="mb-2"
                placeholder="Company Name" />
            <BaseInput :error="errors.position" v-model="form.position" name="position" class="mb-2"
                placeholder="Work Position" />
            <BaseInput :error="errors.notes" v-model="form.notes" name="notes" class="mb-2" placeholder="Notes" />
            <BaseInput :error="errors.address" v-model="form.address" name="address" class="mb-2"
                placeholder="Address" />
            <BaseInput :error="errors.city" v-model="form.city" name="city" class="mb-2" placeholder="City" />
            <BaseInput :error="errors.zipcode" v-model="form.zipcode" name="zipcode" class="mb-2"
                placeholder="Zip/Postal Code" />

            <BaseSelect v-model="form.state" name="state" class="mb-2">
                <option value="" disabled>Select State</option>
                <option v-for="province in provinces" :key="province.code" :value="province.code">
                    {{ province.name }}
                </option>
            </BaseSelect>

            <BaseSelect v-model="form.country_code" name="country_code" class="mb-2" @change="handleChange">
                <option value="" disabled>Select Country</option>
                <option v-for="country in countries" :key="country.code" :value="country.code">
                    {{ country.name }}
                </option>
            </BaseSelect>

            <p v-if="error" style="color: red;">{{ error }}</p>
        </BaseOffCanvas>

    </div>
</template>

<script setup>
import { onMounted, ref, reactive, computed } from 'vue'
import { PlusIcon, EnvelopeIcon, PhoneIcon, BuildingOffice2Icon } from '@heroicons/vue/24/solid'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseOffCanvas from '@/components/base/BaseOffCanvas.vue'
import BaseSelect from '@/components/base/BaseSelect.vue'
import countries from '@/data/countries.json';

const showOffCanvas = ref(false)
const editingId = ref(null)
const contacts = ref([])
const search = ref('')

onMounted(async () => {
    const response = await listRecords({
        module: 'Contact',
        filters: {},
        page: 1,
        perPage: 20,
        sortBy: 'name',
        sortOrder: 'asc',
    });

    contacts.value = response.data;

    try {
        const accRes = await listRecords({ module: 'Account', filters: {}, page: 1, perPage: 100, sortBy: 'name', sortOrder: 'asc' })
        accountOptions.value = accRes.data
    } catch (e) {}
})

const provinces = ref([])
const country_code = ref('')
const accountOptions = ref([])

const errors = reactive({
    name: '',
    email: '',
    phone: '',
    company_name: '',
    position: '',
    notes: '',
    address: '',
    city: '',
    zipcode: '',
    state: '',
    country_code: '',
})

const form = reactive({
    account_id: '',
    name: '',
    email: '',
    phone: '',
    company_name: '',
    position: '',
    notes: '',
    address: '',
    city: '',
    zipcode: '',
    state: '',
    country_code: '',
})

const loading = ref(false)
const error = ref(null)

const offcanvasTitle = computed(() => (editingId.value ? 'Edit Contact' : 'Create Contact'))

const resetForm = () => {
    Object.assign(form, {
        name: '',
        email: '',
        phone: '',
        company_name: '',
        position: '',
        notes: '',
        address: '',
        city: '',
        zipcode: '',
        state: '',
        country_code: '',
    })
    Object.keys(errors).forEach((k) => (errors[k] = ''))
    provinces.value = []
}

const setProvincesForCountryCode = async (code) => {
    if (!code) {
        provinces.value = []
        return
    }
    const match = countries.find(c => c.code === code)
    if (!match) {
        provinces.value = []
        return
    }
    const countryKey = match.name.toLowerCase().replace(/\s+/g, '-')
    try {
        const module = await import(`../../data/countries/${countryKey}.json`)
        provinces.value = module.default
    } catch (e) {
        provinces.value = []
    }
}

const openCreate = () => {
    editingId.value = null
    resetForm()
    showOffCanvas.value = true
}

const openEdit = async (contact) => {
    editingId.value = contact.id
    Object.assign(form, {
        account_id: contact.account_id || '',
        name: contact.name || '',
        email: contact.email || '',
        phone: contact.phone || '',
        company_name: contact.company_name || '',
        position: contact.position || '',
        notes: contact.notes || '',
        address: contact.address || '',
        city: contact.city || '',
        zipcode: contact.zipcode || '',
        state: contact.state || '',
        country_code: contact.country_code || '',
    })
    await setProvincesForCountryCode(form.country_code)
    showOffCanvas.value = true
}

const handleChange = async (e) => {
    const selectedOption = e.target.selectedOptions[0]
    const countryKey = selectedOption.textContent
        .toLowerCase()
        .replace(/\s+/g, '-')

    try {
        const module = await import(`../../data/countries/${countryKey}.json`)
        provinces.value = module.default
    } catch (e) {
        provinces.value = []
    }
}

import { useToast } from "vue-toastification";
import { createRecord, updateRecord, jsonRpcCall, listRecords } from '@/services/jsonrpc'
const toast = useToast();

const submitForm = async () => {
  error.value = null
  loading.value = true

  const contactData = { ...form }

  try {
    if (editingId.value) {
        const updated = await updateRecord('Contact', editingId.value, contactData)
        const idx = contacts.value.findIndex(c => c.id === editingId.value)
        if (idx !== -1) contacts.value[idx] = updated
        Object.keys(errors).forEach((key) => (errors[key] = ''))
        showOffCanvas.value = false
        editingId.value = null
        toast.success("Contact updated.")
    } else {
        const response = await createRecord('Contact', contactData)
        contacts.value.unshift(response)
        Object.keys(errors).forEach((key) => (errors[key] = ''))
        showOffCanvas.value = false
        toast.success("Contact saved.")
    }
  } catch (e) {
    const msg = e?.message || 'Error creating contact'
    error.value = msg
    toast.error(msg)

    if (e?.validation && typeof e.validation === 'object') {
      Object.keys(errors).forEach((key) => (errors[key] = ''))
      for (const [field, messages] of Object.entries(e.validation)) {
        errors[field] = Array.isArray(messages) ? messages[0] : String(messages)
      }
    }

    console.debug('Create contact failed:', e)
  } finally {
    loading.value = false
  }
}

const fetchContacts = async () => {
  const resp = await listRecords({ module: 'Contact', filters: search.value ? { name: search.value } : {}, page: 1, perPage: 20, sortBy: 'name', sortOrder: 'asc' })
  contacts.value = resp.data
}

let debounceTimer
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchContacts, 300)
}

</script>


<style lang="less" scoped>
.page {
    padding: 1rem;

    .page-header {
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;

        h1,
        p {
            margin: 0;
        }

        &-title {
            font-size: 2rem;
            margin: 0;
        }

        &-subtitle {
            color: #333333;
            font-size: 0.9rem;
        }

        button {
            .icon {
                width: 1.3rem;
            }
        }

    }

    .offcanvas-header {
        margin-bottom: 10px;
        top: 0;
        position: sticky;
    }

    // contact grdi
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1rem;

        &-item {
            background-color: #ffffff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
            cursor: pointer;

            &-header {
                display: flex;
                align-items: center;
                gap: 10px;

                p {
                    font-weight: 500;
                }

                small {
                    color: #333333;
                    font-size: 0.8rem;
                }

                * {
                    margin: 0;
                    padding: 0;
                }

                .no-image {
                    width: 50px;
                    height: 50px;
                    font-weight: bold;
                    border-radius: 50%;
                    background-color: var(--clr-primary);
                    font-size: 2rem;
                    display: grid;
                    place-content: center;
                    color: #f0f0f0;
                }
            }


            &-content {
                margin-top: 10px;
                border-top: 1px solid #f0f0f0;
                padding: 10px 0;

                p {
                    @color: #807676;
                    margin: 0;
                    padding: 0;
                    margin-bottom: 5px;
                    font-size: 0.8rem;
                    color: @color;
                    display: flex;
                    gap: 10px;
                    align-items: center;

                    & .icon {
                        width: 1rem;
                        color: @color;
                    }

                    a {
                        color: @color;
                        text-decoration: underline;
                    }
                }
            }
        }
    }
}
</style>
