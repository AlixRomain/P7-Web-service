<?php

namespace App\Controller;

use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class ClientsController extends AbstractFOSRestController
{
    private $em;
    private $repoClients;

    public function __construct( EntityManagerInterface $em, ClientRepository  $repoClients){
        $this->em = $em;
        $this->repoClients = $repoClients;
    }
    /**
     * Show a clients list
     * @Rest\Get(
     *     path = "/api/clients",
     *     name = "all_clients_show",
     * )
     * @Rest\View(serializerGroups={"Default"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getClientsList(Client $clients): Client
    {
        return $clients;
    }

    /**
     * Show one client details and this users
     * @Rest\Get(
     *     path = "/api/clients/{id}",
     *     name = "client_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"Default"})
     * @IsGranted("ROLE_USER")
     */
    public function getOneClients(Client $client): Client
    {
        return $client;
    }

    /**
     * Add one client corporation by admin
     * @Rest\Post(
     *     path = "/api/admin/client",
     *     name = "add_client",
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter(
     *     "client",
     *      converter="fos_rest.request_body",
     *      options={
     *         "validator" = {"groups" = "Create"}
     *     }
     * )
     * @throws ResourceValidationException
     * @IsGranted("ROLE_ADMIN")
     */
    public function postAddOneMobile(Client $client, ConstraintViolationList $violations): \FOS\RestBundle\View\View
    {
        if(count($violations)) {
            $message = 'The JSON sent contains invalid data : ' ;

            foreach ($violations as $violation){
                $message .= sprintf(
                    "Field %s: %s",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            throw new ResourceValidationException($message);
            //return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }
        $this->em->persist($client);
        $this->em->flush();
        return $this->view(
            $client,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('tools_show', ['id' => $client->getId()])
            ]
        );
    }
    /**
     * Update one client by admin
     * @Rest\Put(
     *     path = "/api/admin/client/{id}",
     *     name = "update_client",
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter(
     *     "client",
     *      converter="fos_rest.request_body",
     *      options={
     *         "validator" = {"groups" = "Create"}
     *     }
     * )
     * @throws ResourceValidationException
     * @IsGranted("ROLE_ADMIN")
     */
    public function putUpdateOneMobile(Client $client, ConstraintViolationList $violations): \FOS\RestBundle\View\View
    {
        if(count($violations)) {
            $message = 'The JSON sent contains invalid data : ' ;

            foreach ($violations as $violation){
                $message .= sprintf(
                    "Field %s: %s",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            throw new ResourceValidationException($message);
            //return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }
        $this->em->persist($client);
        $this->em->flush();
        return $this->view(
            $client,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('tools_show', ['id' => $client->getId()])
            ]
        );
    }

    /**
     * Delete one client by admin
     * @Rest\Delete(
     *     path = "/api/admin/client/{id}",
     *     name = "delete_client",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 204)
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteClientsMethod(Client $client)
    {
        $this->em->remove($client);
        $this->em->flush();
    }
}
