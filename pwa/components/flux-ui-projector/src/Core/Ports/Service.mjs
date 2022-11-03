export default class Service {
    /**
     * @var {Aggregate}
     */
    aggregate;

    projectData(
      projectionName
    ) {
        this.aggregate.projectData(projectionName)
    }
}