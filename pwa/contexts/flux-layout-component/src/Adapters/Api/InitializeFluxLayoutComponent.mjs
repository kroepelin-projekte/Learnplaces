export default class InitializeFluxLayoutComponent {

    /** @var {{behaviorsDirectoryPath: string}} payload */
    payload;

    /**
     * @param {string} behaviorsDirectoryPath
     */
    static new(behaviorsDirectoryPath) {
        return new this(
            {
                behaviorsDirectoryPath: behaviorsDirectoryPath
            }
        )
    }

    constructor(payload) {
        this.payload = payload;
    }

}