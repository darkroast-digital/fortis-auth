<template>
    <div>
        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" v-model="slug" autocomplete="off">
        </div>
        <div class="field slug-field">
            <label for="slug">Slug:</label>
            <input type="text" name="slug" v-bind:value="slugged" autocomplete="off" tabindex="-1">
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

        created() {
            axios.get('full.dev/api/post/')
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
