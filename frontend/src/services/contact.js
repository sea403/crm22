import axios from "./api";
import { getToken } from "./auth";

const setAuthHeader = () => {
  axios.defaults.headers.common["Authorization"] = `Bearer ${getToken()}`;
};

export const getContacts = async () => {
  try {
    setAuthHeader();
    const res = await axios.get("/contacts");
    return res.data.data;
  } catch (e) {}
};

export const getContact = async (id) => {
  try {
    setAuthHeader();
    const res = await axios.get(`/contacts/${id}`);
    return res.data;
  } catch (e) {
    console.error(`Failed to fetch contact #${id}:`, e);
    return null;
  }
};

export const createContact = async (contact) => {
  try {
    setAuthHeader();
    const res = await axios.post("/contacts", contact);
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

export const updateContact = async (id, contact) => {
  try {
    setAuthHeader();
    const res = await axios.put(`/contacts/${id}`, contact);
    return res.data;
  } catch (e) {
    console.error(`Failed to update contact #${id}:`, e);
    return null;
  }
};

export const deleteContact = async (id) => {
  try {
    setAuthHeader();
    const res = await axios.delete(`/contacts/${id}`);
    return res.data;
  } catch (e) {
    console.error(`Failed to delete contact #${id}:`, e);
    return null;
  }
};
