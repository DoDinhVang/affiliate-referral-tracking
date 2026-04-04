import React from "react";
import ReactDOM from "react-dom/client";
import createApp from "@shopify/app-bridge";
import { getSessionToken } from "@shopify/app-bridge/utilities";

console.log("host", new URLSearchParams(location.search).get("host"));
console.log("VITE_SHOPIFY_API_KEY:", import.meta.env.VITE_SHOPIFY_API_KEY);

export function App() {
    const [sessionToken, setSessionToken] = React.useState(null);
    const [appBridge, setAppBridge] = React.useState(null);

    React.useEffect(() => {
        if (new URLSearchParams(location.search).get("host")) {
            const app = createApp({
                apiKey: import.meta.env.VITE_SHOPIFY_API_KEY,
                host: new URLSearchParams(location.search).get("host"),
            });

            console.log("App created:", import.meta.env.VITE_SHOPIFY_API_KEY);
            setAppBridge(app);

            async function init() {
                try {
                    console.log("Starting init function...");
                    const token = await getSessionToken(app);
                    console.log("my-session-token", token);
                    setSessionToken(token);
                } catch (error) {
                    console.error("Error in init function:", error);
                }
            }
            init();
        }
    }, []);

    return (
        <div>
            <h1>Shopify App with React</h1>
            <p>Session Token: {sessionToken ? sessionToken : "Loading..."}</p>
        </div>
    );
}

if (document.getElementById("app")) {
    const root = ReactDOM.createRoot(document.getElementById("app"));
    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>,
    );
}
