export class ActionType {
    static RECEIVES = new ActionType("receives")
    static SENDS = new ActionType("sends")

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