import React from "react";
import { App } from "./App";

if (document.getElementById("app")) {
    const root = ReactDOM.createRoot(document.getElementById("app"));
    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>,
    );
}
