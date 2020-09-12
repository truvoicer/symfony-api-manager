<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestConfig;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceRequestResponseKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceRequest[]    findAll()
 * @method ServiceRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequest::class);
    }

    public function findByQuery($query)
    {
        $query = $this->createQueryBuilder('sr')
            ->where("sr.service_request_label LIKE :query")
            ->orWhere("sr.service_request_name LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
        return $query;
    }

    public function getServiceRequestByProvider(Provider $provider, string $sort, string $order, int $count) {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        $query->where("p.provider = :provider")
            ->setParameter("provider", $provider);
        return $query->getQuery()
            ->getResult()
            ;
    }
    public function getRequestByName(Provider $provider, string $serviceRequestName) {
        return $this->getEntityManager()
            ->createQuery("SELECT sr from App\Entity\ServiceRequest sr
                            JOIN App\Entity\Service s 
                            WHERE sr.service = s
                            AND sr.provider = :provider 
                            AND sr.service_request_name = :serviceRequestName")
            ->setParameter("provider", $provider)
            ->setParameter("serviceRequestName", $serviceRequestName)
            ->getOneOrNullResult();
    }

    public function save(ServiceRequest $service)
    {
        $this->getEntityManager()->persist($service);
        $this->getEntityManager()->flush();
        return $service;
    }

    public function duplicateServiceRequest(ServiceRequest $serviceRequest, array $data)
    {
        $newServiceRequest = new ServiceRequest();
        $newServiceRequest->setServiceRequestName($data['item_name']);
        $newServiceRequest->setServiceRequestLabel($data['item_label']);
        $newServiceRequest->setService($serviceRequest->getService());
        $newServiceRequest->setProvider($serviceRequest->getProvider());

        $requestResponseKeys = $serviceRequest->getServiceRequestResponseKeys();
        foreach ($requestResponseKeys as $item) {
            $responseKey = new ServiceRequestResponseKey();
            $responseKey->setServiceRequest($newServiceRequest);
            $responseKey->setServiceResponseKey($item->getServiceResponseKey());
            $responseKey->setResponseKeyValue($item->getResponseKeyValue());
            $responseKey->setShowInResponse($item->getShowInResponse());
            $responseKey->setHasArrayValue($item->getHasArrayValue());
            $responseKey->setArrayKeys($item->getArrayKeys());
            $responseKey->setListItem($item->getListItem());
            $responseKey->setReturnDataType($item->getReturnDataType());
            $responseKey->setAppendExtraData($item->getAppendExtraData());
            $responseKey->setAppendExtraDataValue($item->getAppendExtraDataValue());
            $responseKey->setPrependExtraData($item->getPrependExtraData());
            $responseKey->setPrependExtraDataValue($item->getPrependExtraDataValue());
            $newServiceRequest->addServiceRequestResponseKey($responseKey);
            $this->getEntityManager()->persist($responseKey);
        }

        $requestConfig = $serviceRequest->getServiceRequestConfigs();
        foreach ($requestConfig as $item) {
            $serviceRequestConfig = new ServiceRequestConfig();
            $serviceRequestConfig->setItemName($item->getItemName());
            $serviceRequestConfig->setItemValue($item->getItemValue());
            $serviceRequestConfig->setValueType($item->getValueType());
            $serviceRequestConfig->setServiceRequest($newServiceRequest);
            $newServiceRequest->addServiceRequestConfig($serviceRequestConfig);
            $this->getEntityManager()->persist($serviceRequestConfig);
        }

        $requestParams = $serviceRequest->getServiceRequestParameters();
        foreach ($requestParams as $item) {
            $serviceRequestParams = new ServiceRequestParameter();
            $serviceRequestParams->setParameterName($item->getParameterName());
            $serviceRequestParams->setParameterValue($item->getParameterValue());
            $serviceRequestParams->setServiceRequest($newServiceRequest);
            $newServiceRequest->addServiceRequestParameter($serviceRequestParams);
            $this->getEntityManager()->persist($serviceRequestParams);
        }
        $this->getEntityManager()->persist($newServiceRequest);
        $this->getEntityManager()->flush();
        return $serviceRequest;
    }

    public function delete(ServiceRequest $service) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $service;
    }
}
