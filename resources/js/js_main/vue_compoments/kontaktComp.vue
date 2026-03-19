<script setup>

</script>

<template>


    <div  class="forms-wrapper" >
        <div class="Formcontainer"  >
            <form >
                <div class="form-group">
                    <label>Ihr Name</label>
                    <input type="text" v-model="name" />
                </div>
                <div class="form-group">
                <label>Ihr Password</label>
                <input type="password" >
                </div>
            </form>
        </div>


    </div>





</template>

<style scoped lang="scss">
@use "../../../../resources/css/css_main/defaultForm.scss";
@use "../../../../resources/css/css_main/defaultButton";
</style>
