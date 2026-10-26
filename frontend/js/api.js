const API_BASE = "http://localhost:8000/api";

async function api(endpoint, { method = "GET", body } = {}) {
  let res;
  try {
    res = await fetch(`${API_BASE}/${endpoint}`, {
      method,
      credentials: "include", 
      headers: body ? { "Content-Type": "application/json" } : {},
      body: body ? JSON.stringify(body) : undefined,
    });
  } catch (e) {
    return {
      ok: false,
      status: 0,
      data: { message: "Cannot reach the server." },
    };
  }
  const data = await res
    .json()
    .catch(() => ({ message: "Invalid server response." }));
  return { ok: res.ok, status: res.status, data };
}

function showError(message) {
  const el = document.getElementById("error");
  el.textContent = message;
  el.hidden = !message;
}

async function requireLogin() {
  const { ok, data } = await api("me.php");
  if (!ok) {
    window.location.href = "login.html";
    return null;
  }
  return data.user;
}
