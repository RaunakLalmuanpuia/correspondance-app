<template>
    <q-page class="container" padding>
        <div class="flex justify-between items-center">
            <div>
                <div class="stitle">New Issue</div>
            </div>
        </div>
        <br/>

        <q-form class="row q-col-gutter-sm" style="max-width: 720px" @submit="submit">
            <div class="col-xs-12 col-sm-6">

                <q-input v-model="form.letter_addressee_main"
                         :error="!!form.errors?.letter_addressee_main"
                         :error-message="form.errors?.letter_addressee_main?.toString()"
                         :rules="[
                             val=>!!val || 'Letter Addressee(Main)'
                         ]"
                         autogrow
                         type="textarea"
                         bg-color="white"
                         label="Letter Addressee(Main)"
                         no-error-icon
                         outlined
                />

            </div>
            <!-- Letter Addressee (Copy-to) Section -->
            <div class="col-xs-12 col-sm-6">
                <q-input
                    v-model="newCopyTo"
                    label="Letter Addressee (Copy-to)"
                    bg-color="white"
                    outlined
                    @keyup.enter="addCopyTo"
                >
                    <template v-slot:append>
                        <q-btn
                            flat
                            dense
                            icon="add"
                            color="primary"
                            @click="addCopyTo"
                        />
                    </template>
                </q-input>

                <!-- Display added items -->
                <div class="q-mt-sm flex flex-wrap gap-2">
                    <q-chip
                        v-for="(item, index) in form.letter_addressee_copy_to"
                        :key="index"
                        removable
                        color="primary"
                        text-color="white"
                        @remove="removeCopyTo(index)"
                        class="q-ma-xs"
                    >
                        {{ item }}
                    </q-chip>
                </div>

                <!-- Validation Error -->
                <div v-if="form.errors?.letter_addressee_copy_to" class="text-negative text-caption q-mt-xs">
                    {{ form.errors.letter_addressee_copy_to.toString() }}
                </div>
            </div>
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
                />
            </div>
            <div class="col-xs-12 mt-2">
                <q-input v-model="form.letter_no"
                         :error="!!form.errors?.letter_no"
                         :error-message="form.errors?.letter_no?.toString()"
                         :rules="[
                             val=>!!val || 'Letter No'
                         ]"
                         bg-color="white"
                         label="Letter No"
                         no-error-icon
                         outlined
                />
            </div>
            <div class="col-xs-12">
                <q-select v-model="form.cell"
                          :error="!!form.errors?.cell_id"
                          :error-message="form.errors?.cell_id?.toString()"
                          :options="designated_cells"
                          :rules="[
                             val=>!!val || 'Designated Cell is required'
                         ]"
                          bg-color="white"
                          label="Designated Cell"
                          no-error-icon
                          outlined
                />
            </div>
            <div class="col-xs-12">
                <div class="flex q-gutter-sm">
                    <q-btn :loading="state.submitting" class="sized-btn" color="primary" label="Save" type="submit"/>
                    <q-btn class="sized-btn" color="negative" label="Cancel" outline
                           @click="$inertia.get(route('issues.index'))"/>
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

defineProps(['designated_cells'])
const state = reactive({
    submitting: false
})
const form = useForm({
    letter_addressee_main: '',
    letter_addressee_copy_to: [],
    subject: '',
    letter_no: '',
    letter_date: '',
    cell: null,

});

const newCopyTo = ref('')
function addCopyTo() {
    const value = newCopyTo.value.trim()
    if (value && !form.letter_addressee_copy_to.includes(value)) {
        form.letter_addressee_copy_to.push(value)
    }
    newCopyTo.value = ''
}
function removeCopyTo(index) {
    form.letter_addressee_copy_to.splice(index, 1)
}
const submit = e => {
    form.transform(data => ({cell_id: data?.cell?.value, ...data}))
        .post(route('issues.store'), {
            preserveState: false,
            onStart: params => state.submitting = true,
            onFinish: params => state.submitting = false
        })
}

</script>
