export default class Aggregate {

  #elementId;

  #projectionApi = "http://127.3.3.3/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/api.php";
  #domainEventPublisher

  async projectData(
    projectionName,
  ) {
    const data = {};

    const response = await (
      await fetch(
        this.#projectionApi + "project" + projectionName
      )
    );
    if (response.ok) {
      const json = await response.json();
      this.#applyDataProjected(
        {
          "elementId": this.#elementId,
          "data": json.data
        }
      )
    } else {
      alert("HTTP-Error: " + response.status);
    }
  }

  #applyDataProjected(event) {

    this.#domainEventPublisher.publish(
      dataProjected.projectionName + 'Projected',
      dataProjected
    )

  }


}