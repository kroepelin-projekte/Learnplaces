// Season enums can be grouped as static members of a class
export class DomainMessageName {
    // Create new instances of the same class as static attributes
    static APPLICATION_SUBSCRIBED_TO_CHANNEL = new DomainMessageName("appSubscribedToChannel")

    constructor(value) {
        this.value = value
    }

    /**
     * @param messageName
     * @returns {boolean}
     */
    match(messageName) {
        return (messageName.value === this.value);
    }
}