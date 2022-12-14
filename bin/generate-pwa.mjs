#!/usr/bin/env node
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";

try {
    const json_api = (await import("../libs/flux-json-api/src/Adapter/Api/JsonApi.mjs")).JsonApi.new();

    const localization_api = (await import("../libs/flux-localization-api/src/Adapter/Api/LocalizationApi.mjs")).LocalizationApi.new(
        json_api
    );
    await localization_api.init();

    const pwa_generator_api = (await import("../libs/flux-pwa-generator-api/src/Adapter/Api/PwaGeneratorApi.mjs")).PwaGeneratorApi.new(
        json_api,
        localization_api
    );

    const __dirname = dirname(fileURLToPath(import.meta.url));

    const web_root = join(__dirname, "..", "pwa");

    await pwa_generator_api.generateManifestJsons(
        join(web_root, "appmanifest.webmanifest.json"),
        join(web_root, "localization")
    );

    await pwa_generator_api.generateServiceWorker(
        web_root,
        join(web_root, "serviceworker.mjs"),
        "learnplaces-application-",
        join(web_root, "serviceworker-template.mjs")
    );
} catch (error) {
    console.error(error);

    process.exit(1);
}
