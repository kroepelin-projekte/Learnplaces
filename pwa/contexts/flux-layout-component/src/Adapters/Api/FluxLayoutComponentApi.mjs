import OutboundAdapter from './OutboundAdapter.mjs';
import Aggregate from "../../Core/Aggregate.mjs";

export default class FluxLayoutComponentApi {

    /** @var {string} */
    #name = "flux-layout-component-api"
    /** @var {Aggregate} */
    #aggregate;

    #apiBehaviors;
    /** @var {OutboundAdapter} */
    #outbounds;

    /**
     * @return {FluxLayoutComponentApi}
     * @param {InitializeFluxLayoutComponent} initializeFluxLayoutComponent
     */
    static async initialize(initializeFluxLayoutComponent) {
        const obj = new this();
        obj.#outbounds = OutboundAdapter.initialize(initializeFluxLayoutComponent.payload);
        obj.#apiBehaviors = await obj.#outbounds.getApiBehaviors();
        obj.#aggregate = await Aggregate.initialize(obj.#outbounds);
        await obj.#initGeneralReactors();
        return obj;
    }

    constructor() { }

    async #initGeneralReactors() {
        Object.entries(this.#apiBehaviors.reactsOn).forEach(([reactionId, reaction]) => {

            console.log(reaction);

            if (reaction.address.includes('{name}')) {
                return;
            }
            if (reaction.address.includes('{$slot}')) {
                return;
            }

            this.#outbounds.onRegister(this.#name)(
                reaction.address,
                (message) => this.#reaction(reaction, message),
                true
            );
        });
    }

    async #initNamedReactors() {
        const reactsOn = this.#apiBehaviors.reactsOn;
        Object.entries(reactsOn).forEach(([reactionId, reaction]) => {
            if (reactionAddress.includes('{$name}') === false) {
                return;
            }
            const reactionAddress = reaction.address.replace("{$name}", this.#name)
            this.#outbounds.onRegister(this.#name)(
                reactionAddress,
                (message) => this.#reaction(reaction, message),
                true
            );
        });
    }


    async #reaction(reaction, message) {

        const payload = {
            ...reaction.payload,
            ...message.payload
        };
        try {
            //const replyToAddress = reaction.replyTo.replace("{$name}", this.#name)
            this.#aggregate[reaction.operationId](payload);
        } catch (e) {
            console.log(this.#aggregate);
            console.error(reaction.operationId + " " + e)
        }
    }

    async #initSlotReactors(
        reactionAddress,
        reaction
    ) {
        const template = await this.#outbounds.loadTemplate(this.#name);
        const slots = await template.slots;
        if (slots) {
            Object.entries(slots).forEach(([slotName, slot]) => {
                const slottedReactionAddress = reactionAddress.replace("{$slot}", slotName)

                this.#outbounds.onRegister(this.#name)(
                    slottedReactionAddress,
                    (message) => this.#slotReaction(slotName, reaction, message)
                );
            });
        }
    }

    async #slotReaction(slotName, reaction, message) {

        const payload = {
            ...reaction.payload,
            ...message.payload
        };
        try {
            const replyToAddress = reaction.replyTo.replace("{$name}", this.#name)
            const slottedReplyToAddress = replyToAddress.replace("{$slot}", slotName)
            this.#aggregate[reaction.operationId](slotName, payload, slottedReplyToAddress);


        } catch (e) {
            console.error(reaction.operationId + " " + e)
        }
    }

}