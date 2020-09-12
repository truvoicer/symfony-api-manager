<?php
namespace App\Service;


use App\Entity\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ParameterService
{
    private $entityManager;
    private $httpRequestService;
    private $parameterRepository;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->parameterRepository = $this->entityManager->getRepository(Parameter::class);
    }

    public function getParameterObject(Parameter $parameter, array $data)
    {
        $parameter->setParameterName($data['parameter_name']);
        $parameter->setParameterValue($data['parameter_value']);
        return $parameter;
    }

    public function createParameter(array $data)
    {
        $parameter = $this->getParameterObject(new Parameter(), $data);
        if ($this->httpRequestService->validateData(
            $parameter
        )) {
            return $this->parameterRepository->saveParameter($parameter);
        }
        return false;
    }

    public function updateParameter(array $data)
    {
        $parameter = $this->parameterRepository->findOneBy(["id" => $data["id"]]);
        if ($parameter === null) {
            throw new BadRequestHttpException(sprintf("Parameter id:%d not found in database.", $data["id"]));
        }
        if ($this->httpRequestService->validateData(
            $this->getParameterObject($parameter, $data)
        )) {
            return $this->parameterRepository->saveParameter($parameter);
        }
        return false;
    }

    public function deleteParameter(int $parameterId) {
        $parameter = $this->parameterRepository->findOneBy(["id" => $parameterId]);
        if ($parameter === null) {
            throw new BadRequestHttpException(sprintf("Parameter id: %s not found in database.", $parameterId));
        }
        return $this->parameterRepository->deleteParameter($parameter);
    }

}