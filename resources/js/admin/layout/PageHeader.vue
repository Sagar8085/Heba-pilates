<template>
    <section class="page-header page-header--no-bottom">
        <div class="wrapper">
            <div class="page-header__col">
                <h1 class="page-header__title">
                    <div class="icon">
                        <i class="material-icons">{{ icon }}</i>
                    </div>
                    {{ title }}
                </h1>

                <h2 class="page-header__sub">{{ subtitle }}</h2>
            </div>

            <div v-if="$slots.default" class="page-header__col">
                <slot></slot>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    props: {
        icon: String,
        title: String,
        subtitle: String
    }
}
</script>