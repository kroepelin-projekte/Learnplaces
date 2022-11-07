#!/usr/bin/env node
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";

try {
    const json_api = (await import("../generate-pwa-libs/flux-json-api/src/Adapter/Api/JsonApi.mjs")).JsonApi.new();
    await json_api.init();

    const pwa_generator_api = (await import("../generate-pwa-libs/flux-pwa-generator-api/src/Adapter/Api/PwaGeneratorApi.mjs")).PwaGeneratorApi.new(
        json_api
    );
    await pwa_generator_api.init();

    const __dirname = dirname(fileURLToPath(import.meta.url));

    const web_root = join(__dirname, "..", "pwa");

    await pwa_generator_api.generateServiceWorker(
        web_root,
        join(web_root, "serviceworker-template.mjs"),
        join(web_root, "serviceworker.mjs"),
        {}
    );
} catch (error) {
    console.error(error);

    process.exit(1);
}
