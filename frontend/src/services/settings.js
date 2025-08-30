import axios from "./api";
import { getToken } from "./auth";

const setAuthHeader = () => {
  axios.defaults.headers.common["Authorization"] = `Bearer ${getToken()}`;
};

export const getSettings = async () => {
  try {
    setAuthHeader();
    const res = await axios.get("/settings/general");
    return res.data.data;
  } catch (e) {
    console.error("Failed to fetch settings:", e);
    return null;
  }
};

export const updateSettings = async (payload) => {
  try {
    setAuthHeader();
    const res = await axios.put("/settings/general", payload);
    return res.data.data;
  } catch (e) {
    if (e.response?.status === 422) {
      return {
        error: true,
        errors: e.response.data.errors,
      };
    }

    return {
      error: true,
      message: "Something went wrong",
    };
  }
};
