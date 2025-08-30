<template>
    <nav class="app-header">
        <ul class="app-header--menu">
            <li>
                <RouterLink active-class="active" to="/home">
                    <HomeIcon class="icon color-primary" />
                    {{ $t('menu.home') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/contacts" active-class="active">
                    <UserIcon class="icon color-slateblue" />
                    {{ $t('menu.allContacts') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/accounts" active-class="active">
                    <UsersIcon class="icon color-coral" />
                    {{ $t('menu.accounts') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/projects" active-class="active">
                    <Cog6ToothIcon class="icon color-teal" />
                    {{ $t('menu.projects') }}
                </RouterLink>
            </li>
            <li>
                <a href="">
                    <DocumentChartBarIcon class="icon color-teal" />
                    {{ $t('menu.reports') }}
                </a>
            </li>
            <li>
                <RouterLink to="/emails" active-class="active">
                    <CalendarIcon class="icon color-blueviolet" />
                    {{ $t('menu.emails') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/calender" active-class="active">
                    <CalendarIcon class="icon color-blueviolet" />
                    {{ $t('menu.calendar') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/expenses" active-class="active">
                    <BanknotesIcon class="icon color-cadetblue" />
                    {{ $t('menu.expenses') }}
                </RouterLink>
            </li>
            <li>
                <RouterLink to="/settings" active-class="active">
                    <Cog6ToothIcon class="icon color-deepskyblue" />
                    {{ $t('menu.settings') }}
                </RouterLink>
            </li>
        </ul>

        <div class="user-dropdown">
            <a class="user-profile-dropdown-handler" href="#">
                {{ $t('menu.userName') }}
            </a>

            <ul class="user-dropdown-menu">
                <li>
                    <a href="">
                        <UserIcon class="icon color-slateblue" />
                        {{ $t('menu.profile') }}
                    </a>
                </li>
                <li>
                    <a href="">
                        <EyeIcon class="icon color-slateblue" />
                        {{ $t('menu.appearance') }}
                    </a>
                </li>
                <li>
                    <a href="#" @click="handleLogout">
                        <ArrowRightOnRectangleIcon class="icon color-slateblue" />
                        {{ $t('menu.logout') }}
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup>
import { RouterLink, useRouter } from 'vue-router';
import {
    HomeIcon,
    DocumentChartBarIcon,
    UserIcon,
    UsersIcon,
    CalendarIcon,
    Cog6ToothIcon,
    BanknotesIcon,
    ArrowRightOnRectangleIcon,
    EyeIcon
} from '@heroicons/vue/24/solid';
import { useAuth } from '@/composables/useAuth';
const router = useRouter();

const { logout } = useAuth();

const handleLogout = () => {
    logout();
    router.push('/login');
};

</script>


<style lang="less" scoped>
.app-header {
    position: sticky;
    top: 0;
    width: 100%;
    z-index: 999;
    background-color: #ffffff;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;

    .user-dropdown {
        position: relative;

        &:hover .user-dropdown-menu {
            display: block;
        }

        .user-profile-dropdown-handler {
            margin-right: 10px;
            padding: 4px 10px;
            color: #333333;
            cursor: pointer;
        }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            list-style: none;
            background: #f0f0f0;
            padding: 0;
            margin: 0;
            top: 100%;
            right: 4px;
            z-index: 10;
            min-width: 150px;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            background-color: #ffffff;
            border-radius: 4px;
            overflow: hidden;

            li {
                list-style: none;
            }

            a {
                display: flex;
                gap: 10px;
                align-items: center;
                width: 100%;
                padding: 5px;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                color: #333333;

                &.active,
                &:hover {
                    background-color: #eaeaea;
                }

                .icon {
                    width: 1rem;
                }
            }
        }
    }



    ul.app-header--menu {
        margin: 0;
        list-style-type: none;
        padding: 0;
        display: flex;


        a {
            text-decoration: none;
            color: #333333;
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 0.5rem 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

            &.active,
            &:hover {
                background-color: #eaeaea;
            }


            .icon {
                width: 1rem;
                height: 1rem;
            }

        }
    }
}
</style>
