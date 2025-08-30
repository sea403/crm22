import { useUserStore } from "@/src/stores/user";
import { computed, ref } from "vue";

const token = ref(localStorage.getItem("token"));

function login(newToken) {
  token.value = newToken;
  localStorage.setItem("token", newToken);
}

function logout() {
  token.value = null;
  localStorage.removeItem("token");
}

const isLoggedIn = computed(() => !!token.value);

export function useAuth() {
  const userStore = useUserStore();

  return { user: userStore.user, token, isLoggedIn, login, logout };
}
