// services/jsonrpc.js
import axios from "./api";
import { getToken } from "./auth";

let idCounter = 1;

const setAuthHeader = () => {
  const token = getToken();
  if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  } else {
    delete axios.defaults.headers.common["Authorization"];
  }
};

export const jsonRpcCall = async (method, params = {}) => {
  setAuthHeader();

  const requestId = idCounter++;

  try {
    const response = await axios.post("/jsonrpc", {
      jsonrpc: "2.0",
      method,
      params,
      id: requestId,
    });

    const { data } = response;

    if (data?.error) {
      // JSON-RPC error shape
      const rpcErr = data.error;
      const serverMsg = rpcErr.message || "Request failed";
      const detailMsg =
        rpcErr?.data?.message ||
        rpcErr?.data?.error ||
        (rpcErr?.data && typeof rpcErr.data === "string" ? rpcErr.data : null);

      const err = new Error(detailMsg ? `${serverMsg}: ${detailMsg}` : serverMsg);
      err.name = "JsonRpcError";
      err.code = rpcErr.code;
      err.validation = rpcErr?.data?.errors || null; // e.g. { email: ["Email already exists"] }
      throw err;
    }

    return data.result;
  } catch (err) {
    // Axios error with JSON-RPC payload
    const rpc = err?.response?.data?.error;
    if (rpc) {
      const serverMsg = rpc.message || "Request failed";
      const detailMsg =
        rpc?.data?.message ||
        rpc?.data?.error ||
        (rpc?.data && typeof rpc.data === "string" ? rpc.data : null);

      const e2 = new Error(detailMsg ? `${serverMsg}: ${detailMsg}` : serverMsg);
      e2.name = "JsonRpcError";
      e2.code = rpc.code;
      e2.validation = rpc?.data?.errors || null;
      throw e2;
    }

    // Generic REST error body { message: "..." }
    if (err?.response?.data?.message) {
      const e3 = new Error(err.response.data.message);
      throw e3;
    }

    // Network / unknown
    throw err;
  }
};

export const listRecords = async ({
  module,
  filters = {},
  page = 1,
  perPage = 10,
  sortBy = "id",
  sortOrder = "asc",
}) => {
  return jsonRpcCall("listRecords", {
    module,
    filters,
    page,
    perPage,
    sortBy,
    sortOrder,
  });
};

export async function createRecord(module, data) {
  return jsonRpcCall("createRecord", { module, data });
}

export async function updateRecord(module, id, data) {
  return jsonRpcCall("updateRecord", { module, id, data });
}

export async function getRecord(module, id) {
  return jsonRpcCall("getRecord", { module, id });
}
