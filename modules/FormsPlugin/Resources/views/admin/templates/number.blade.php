<template id="field-number-template">
    <div class="col-md-6">
        <label>Výchozí hodnota</label>
        <div class="form-group">
            <input type="text" class="form-control" v-model="options.value">
        </div>
    </div>

    <div class="col-md-6">
        <label>Minimum</label>
        <div class="form-group">
            <input type="number" class="form-control" v-model="options.min">
        </div>
    </div>

    <div class="col-md-6">
        <label>Maximum</label>
        <div class="form-group">
            <input type="number" class="form-control" v-model="options.max">
        </div>
    </div>
</template>