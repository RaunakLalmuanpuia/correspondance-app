<template>
    <q-page class="container" padding>
        <div class="flex justify-between items-center">
            <div>
                <div class="stitle">Edit Receipt</div>
            </div>
        </div>
        <br/>

        <q-form class="row q-col-gutter-sm" style="max-width: 720px" @submit="submit">
            <div class="col-xs-12">
                <q-input v-model="form.subject"
                         :error="!!form.errors?.subject"
                         :error-message="form.errors?.subject?.toString()"
                         :rules="[
                             val=>!!val || 'Subject is required'
                         ]"
                         bg-color="white"
                         label="Subject"
                         no-error-icon
                         outlined
                         autogrow
                         type="textarea"
                />
            </div>

            <div class="col-xs-12 col-sm-6">
                <q-input v-model="form.letter_no"
                         :error="!!form.errors?.letter_no"
                         :error-message="form.errors?.letter_no?.toString()"
                         :rules="[
                             val=>!!val || 'Letter No. is required'
                         ]"
                         bg-color="white"
                         label="Letter No."
                         no-error-icon
                         outlined
                />
            </div>

            <div class="col-xs-12 col-sm-6">
                <q-input
                    v-model="letterDateDisplay"
                    label="Letter Date (DD/MM/YYYY)"
                    outlined
                    bg-color="white"
                    readonly
                    :error="!!form.errors?.letter_date"
                    :error-message="form.errors?.letter_date?.toString()"
                    no-error-icon
                    @click="popupRef.show()"
                >
                    <template #append>
                        <q-icon name="event" class="cursor-pointer">
                            <q-popup-proxy  ref="popupRef" cover transition-show="scale" transition-hide="scale">
                                <q-date
                                    v-model="form.letter_date"
                                    mask="YYYY-MM-DD"
                                    @update:model-value="updateLetterDate"
                                />
                            </q-popup-proxy>
                        </q-icon>
                    </template>
                </q-input>
<!--                <q-input-->
<!--                    v-model="letterDateDisplay"-->
<!--                    label="Letter Date (DD/MM/YYYY)"-->
<!--                    outlined-->
<!--                    bg-color="white"-->
<!--                    mask="##/##/####"-->
<!--                    fill-mask-->
<!--                    placeholder="dd/mm/yyyy"-->
<!--                    :error="!!form.errors?.letter_date"-->
<!--                    :error-message="form.errors?.letter_date?.toString()"-->
<!--                    no-error-icon-->
<!--                    :rules="[-->
<!--                      val => !!val || 'Letter Date is required',-->
<!--                      val => isValidDisplayDate(val) || 'The letter date field must be a valid date.'-->
<!--                    ]"-->
<!--                />-->
            </div>

            <div class="col-xs-12">

                <q-input v-model="form.received_from"
                         :error="!!form.errors?.received_from"
                         :error-message="form.errors?.received_from?.toString()"
                         :rules="[
                             val=>!!val || 'Received From is required'
                         ]"
                         autogrow
                         type="textarea"
                         bg-color="white"
                         label="Received From"
                         no-error-icon
                         outlined
                />

            </div>



            <div class="col-xs-12">
                <q-select v-model="form.cell"
                          :error="!!form.errors?.cell_id"
                          :error-message="form.errors?.cell_id?.toString()"
                          :options="designated_cells"
                          bg-color="white"
                          label="Designated Cell"
                          no-error-icon
                          outlined
                />
            </div>
            <div class="col-xs-12">

                <q-input v-model="form.name_of_da"
                         :error="!!form.errors?.name_of_da"
                         :error-message="form.errors?.name_of_da?.toString()"
                         bg-color="white"
                         label="Name of D.A"
                         no-error-icon
                         outlined
                />

            </div>

            <div class="col-xs-12">
                <div class="flex q-gutter-sm">
                    <q-btn :loading="state.submitting" class="sized-btn" color="primary" label="Save" type="submit"/>
                    <q-btn class="sized-btn" color="negative" label="Cancel" outline
                           @click="$inertia.get(route('receipts.index'))"/>
                </div>
            </div>

        </q-form>


    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {reactive, ref} from "vue";

defineOptions({layout:BackendLayout})

const props = defineProps(['designated_cells','data'])
const state = reactive({
    submitting: false
})
const popupRef = ref(null)
const form = useForm({
    subject:  props.data?.subject || '',
    letter_no: props.data?.letter_no || '',
    letter_date: props.data?.letter_date || '',
    received_from: props.data?.received_from || '',
    cell: { value: props?.data?.cell_id, label: props?.data?.cell?.name },
    name_of_da:props.data?.name_of_da || '',

});
/* DISPLAY FIELD (dd/mm/yyyy) */
const letterDateDisplay = ref(
    formatDMY(props.data?.letter_date)
);

/* Format yyyy-mm-dd → dd/mm/yyyy */
function formatDMY(iso) {
    if (!iso) return "";
    const [y, m, d] = iso.split("-");
    return `${d}/${m}/${y}`;
}

/* Update display when q-date changes */
function updateLetterDate(val) {
    letterDateDisplay.value = formatDMY(val);
}
const submit = e => {
    form.transform(data => ({cell_id: data?.cell?.value, ...data}))
        .put(route('receipts.update',props.data.id), {
            preserveState: true,
            onStart: params => state.submitting = true,
            onFinish: params => state.submitting = false
        })
}

</script>
