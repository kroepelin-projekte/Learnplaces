export class ReferenceDefinition {

    /**
     * @param referenceResolvedCallback
     * @param pointerString
     * @returns {ReferenceDefinition}
     */
    constructor(
        referenceResolvedCallback,
        pointerString
    ) {

    }

    /**
     * @param referenceResolvedCallback
     * @param pointerString
     * @returns {ReferenceDefinition}
     */
    static new(
        referenceResolvedCallback,
        pointerString
    ) {
        return new this(referenceResolvedCallback, pointerString)
    }

    static fromJson(json) {
        return new this()
    }

}