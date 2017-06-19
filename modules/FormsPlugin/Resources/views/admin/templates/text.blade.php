<template id="field-text-template">
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

    <div class="col-md-6">
        <label>Maximální délka</label>
        <div class="form-group">
            <input type="number" class="form-control" v-model="options.maxlength">
        </div>
    </div>
</template>