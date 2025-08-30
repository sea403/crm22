<template>
    <div class="page">

        <div class="setting-page">
            <div class="setting-page_sidebar">
                <ul>
                    <li>
                        <RouterLink to="/settings/general" active-class="active">
                            <Cog6ToothIcon class="icon" />
                            General Settings
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink to="/settings/communication" active-class="active">
                            <PaperAirplaneIcon class="icon" />
                            Communication
                        </RouterLink>
                    </li>
                </ul>
            </div>

            <div class="setting-page__content">
                <RouterView />
            </div>
        </div>

    </div>
</template>

<script setup>
import { Cog6ToothIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid'
</script>

<style lang="less" scoped>
.page {
    padding: 1rem;

    .setting-page {
        display: grid;
        grid-template-columns: 300px auto;
        gap: 10px;

        &_sidebar {
            box-shadow: var(--box-shadow-1);
            min-height: 100vh;
            background-color: #ffffff;

            ul {
                list-style: none;
                margin: 0;
                padding: 0;
                overflow: hidden;
                width: 100%;

                a {
                    gap: 4px;
                    display: flex;
                    padding: 10px;
                    color: #333333;
                    width: 100%;
                    align-items: center;
                    border-bottom: 1px solid #f1f1f1;

                    .icon {
                        width: 1rem;
                    }

                    &.active,
                    &:hover {
                        background-color: #f1f1f1;
                    }

                }
            }
        }

        &__content {
            box-shadow: var(--box-shadow-1);
            padding: 1rem;
            background-color: #ffffff;
        }
    }
}
</style>