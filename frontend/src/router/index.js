import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import LoginView from "@/views/auth/LoginView.vue";
import RegisterView from "@/views/auth/RegisterView.vue";
import ContactList from "@/views/contacts/ContactList.vue";
import AccountList from "@/views/accounts/AccountList.vue";
import CalenderView from "@/views/calender/CalenderView.vue";
import ProjectList from "@/views/projects/ProjectList.vue";
import ExpenseList from "@/views/expenses/ExpenseList.vue";
import SettingsView from "@/views/settings/SettingsView.vue";
import GmailCallBack from "@/views/GmailCallBack.vue";
import ProtectedLayout from "@/components/layouts/ProtectedLayout.vue";
import EmailList from "@/views/emails/EmailList.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "",
      redirect: "/home",
    },
    {
      path: "/login",
      name: "login",
      component: LoginView,
      meta: { guestOnly: true, title: "Login" }, // 👈 Add guestOnly
    },
    {
      path: "/gmail/callback",
      name: "gmail.callback",
      component: GmailCallBack,
      // meta: { guestOnly: true }, // 👈 Add guestOnly
    },
    {
      path: "/register",
      name: "register",
      component: RegisterView,
      meta: { guestOnly: true, title: "Register" }, // 👈 Add guestOnly
    },
    {
      path: "/",
      meta: { requiresAuth: true },
      component: ProtectedLayout,
      children: [
        {
          path: "/home",
          name: "home",
          component: HomeView,
          meta: { title: "Home" }, // 🔐 Auth required
        },
        {
          path: "/contacts",
          name: "contacts",
          component: ContactList,
          meta: { title: "Contacts" },
        },
        {
          path: "/accounts",
          name: "accounts",
          component: AccountList,
          meta: { title: "Accounts" },
        },

        {
          path: "/projects",
          name: "projects",
          component: ProjectList,
          meta: { title: "Projects" },
        },

        {
          path: "/expenses",
          name: "expenses",
          component: ExpenseList,
          meta: { title: "Expenses" },
        },

        {
          path: "/emails",
          name: "emails",
          component: EmailList,
          meta: { title: "Emails" },
        },

        {
          path: "/emails/inbox",
          name: "emails.inbox",
          component: EmailList,
          meta: { title: "Inbox" },
        },

        {
          path: "/calender",
          name: "calnder",
          component: CalenderView,
          meta: { title: "Calender" },
        },

        {
          path: "/settings",
          name: "settings",
          component: SettingsView,
          meta: { title: "Settings" },
          children: [
            {
              path: "general",
              name: "settings.general",
              component: () => import("@/views/settings/GeneralSettings.vue"),
              meta: { title: "General Setting" },
            },
            {
              path: "communication",
              name: "settings.communication",
              component: () =>
                import("@/views/settings/CommunicationSetting.vue"),
              meta: { title: "Communication" },
            },
            {
              path: "",
              redirect: { name: "settings.general" },
            },
          ],
        },
      ],
    },
  ],
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  const defaultTitle = "My CRM";
  document.title = to.meta.title || defaultTitle;

  if (to.meta.requiresAuth && !token) {
    return next("/login");
  }

  if (to.meta.guestOnly && token) {
    return next("/home");
  }

  next();
});

export default router;
