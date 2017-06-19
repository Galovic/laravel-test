<template id="field-email-template">
    <div class="col-md-6">
        <label>Výchozí hodnota</label>
        <div class="form-group">
            <input type="text" class="form-control" v-model="options.value">
        </div>
    </div>

    <div class="col-md-6">
        <label>Placeholder</label>
        <div class="form-group">
            <input type="text" class="form-control" v-model="options.placeholder">
        </div>
    </div>
</template>