<template>
    <div>
        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" v-model="slug" autocomplete="off">
            <small><span>Slug:</span>{{ slugged }}</small>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                slug: ''
            }
        },
        computed: {
            slugged: {
                get: function() {
                    return this.$options.filters.slug(this.slug);
                },
                set: function(val) {
                    this.slug = val;
                }
            }
        }
    }
</script>
