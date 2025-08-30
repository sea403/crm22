<template>
    <div class="emails-page">
        <!-- Sidebar -->
        <div class="sidebar">
            <BaseInput placeholder="Search..." />

            <div class="menu-group">
                <div class="menu-title">Folders</div>
                <div class="menu-item">
                    <InboxIcon class="icon" /> Inbox
                </div>
                <div class="menu-item">
                    <PaperAirplaneIcon class="icon" /> Sent
                </div>
                <div class="menu-item">
                    <ClockIcon class="icon" /> Send later
                </div>
                <div class="menu-item">
                    <DocumentIcon class="icon" /> Drafts
                </div>
                <div class="menu-item">
                    <ShieldExclamationIcon class="icon" /> Spam
                </div>
                <div class="menu-item">
                    <ArchiveBoxIcon class="icon" /> Archive
                </div>
                <div class="menu-item">
                    <TrashIcon class="icon" /> Trash
                </div>
            </div>
        </div>

        <div class="email-list">
            <BaseButton @click="openComposeBox">Compose</BaseButton>

            <div v-for="(mail, i) in mails" :key="i" class="email-item">
                <input type="checkbox" />
                <div class="avatar">AM</div>
                <div class="email-content">
                    <div class="top">
                        <div class="sender">{{ mail.to }} {{ i }}</div>
                        <div class="subject">{{ mail.subject }}</div>
                    </div>

                    <small>{{ mail.created_at }}</small>
                </div>
            </div>
        </div>

        <MailComposeBox v-if="showComposeBox" @close="showComposeBox = false" />
    </div>
</template>

<script setup>
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import MailComposeBox from '@/components/emails/MailComposeBox.vue';
import { listRecords } from '@/services/jsonrpc';
import {
    InboxIcon,
    PaperAirplaneIcon,
    ClockIcon,
    DocumentIcon,
    ShieldExclamationIcon,
    ArchiveBoxIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'
import { onMounted, reactive, ref } from 'vue';
const mails = reactive([]);
const showComposeBox = ref(false);

onMounted(async () => {
    const response = await listRecords({
        module: 'Email',
        filters: {},
        page: 1,
        perPage: 20,
        sortBy: 'created_at',
        sortOrder: 'asc',
    });

    mails.splice(0, mails.length, ...response.data);
});

const openComposeBox = () => {
    showComposeBox.value = !showComposeBox.value;
};

</script>

<style lang="less" scoped>
.emails-page {
    display: flex;
    height: 100%;
    font-family: 'Segoe UI', sans-serif;
    color: #333;

    .sidebar {
        width: 260px;
        background: #f9fafb;
        border-right: 1px solid #ddd;
        padding: 1rem;
        display: flex;
        flex-direction: column;

        .menu-group {
            margin-top: 1rem;
            margin-bottom: 1.5rem;

            .menu-title {
                font-size: 0.8rem;
                font-weight: bold;
                color: #666;
                margin-bottom: 0.4rem;
                text-transform: uppercase;
            }

            .menu-item {
                display: flex;
                align-items: center;
                padding: 0.4rem;
                border-radius: 5px;
                cursor: pointer;
                font-size: 0.9rem;

                &:hover {
                    background: #e5e7eb;
                }

                &.active {
                    background: #d1d5db;
                    font-weight: bold;
                }

                .icon {
                    width: 18px;
                    margin-right: 8px;
                }
            }
        }

    }

    .email-list {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;

        .email-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;

            .avatar {
                background: #c7d2fe;
                color: #3730a3;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                margin: 0 1rem;
            }

            .email-content {
                flex: 1;

                .top {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    justify-content: space-between;
                    font-weight: 500;
                }

            }
        }
    }


}
</style>
