export class BindingType {
    static FLUX_ECO_BROADCAST_CHANNEL = new BindingType("x-flux-eco-broadcast-channel")
    static HTML_BODY = new BindingType("html-body")

    constructor(value) {
        this.value = value
    }

    /**
     * @param {BindingType} bindingType
     * @returns {boolean}
     */
    match(bindingType) {
        return (bindingType.value === this.value);
    }
}