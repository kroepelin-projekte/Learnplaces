export class ReferenceType {
    static INPUT = new ActionType("input")
    static OUTPUT = new ActionType("output")

    constructor(value) {
        this.value = value
    }

    /**
     * @param {ActionType} channelType
     * @returns {boolean}
     */
    match(channelType) {
        return (channelType.value === this.value);
    }
}