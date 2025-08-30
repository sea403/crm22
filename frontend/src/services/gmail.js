import axios from "./api";
import { getToken } from "./auth";

const setAuthHeader = () => {
  axios.defaults.headers.common["Authorization"] = `Bearer ${getToken()}`;
};

export const getGmailConnectUrl = async () => {
  try {
    setAuthHeader();
    const res = await axios.get("/gmail/connect");
    return res.data.url; // Google OAuth URL
  } catch (e) {
    console.error("Failed to get Gmail connect URL:", e);
    return null;
  }
};

export const fetchGmailEmails = async () => {
  try {
    setAuthHeader();
    const res = await axios.get("/emails");
    return res.data;
  } catch (e) {
    console.error("Failed to fetch Gmail emails:", e);
    return [];
  }
};

export const sendGmailEmail = async ({ to, subject, body }) => {
  try {
    setAuthHeader();
    const res = await axios.post("/gmail/send", { to, subject, body });
    return { success: true, message: res.data.message };
  } catch (e) {
    console.error("Failed to send Gmail email:", e);

    return {
      success: false,
      message:
        e.response?.data?.message || "An error occurred while sending email.",
    };
  }
};
