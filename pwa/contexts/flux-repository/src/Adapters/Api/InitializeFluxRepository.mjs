export default class InitializeFluxRepository {

    /** @var {{behaviorsDirectoryPath: string, baseUrl: string}} payload */
    payload;

    /**
     * @param {string} behaviorsDirectoryPath
     * @param {string} baseUrl
     */
    static new(behaviorsDirectoryPath, baseUrl) {
        return new this(
            {
                behaviorsDirectoryPath: behaviorsDirectoryPath,
                baseUrl: baseUrl
            }
        )
    }

    constructor(payload) {
        this.payload = payload;
    }

}