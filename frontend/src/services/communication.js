import axios from "./api";
import { getToken } from "./auth";

const setAuthHeader = () => {
  axios.defaults.headers.common["Authorization"] = `Bearer ${getToken()}`;
};

/** Update the configuration of email imap configuration */
export const updateSMTPConfig = async (payload) => {
  try {
    setAuthHeader();
    const res = await axios.post("/configuration/email/smtp", payload);
    return res.data;
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

/** Update the configuration of email imap configuration */
export const updateIMAPConfig = async (payload) => {
  try {
    setAuthHeader();
    const res = await axios.post("/configuration/email/imap", payload);
    return res.data;
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

/** IMAP Connection test */
export const imapConnectionTest = async (payload) => {
  try {
    setAuthHeader();
    const res = await axios.post(
      "/configuration/email/connection-test",
      payload
    );
    return res.data;
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
