<?php
namespace App\Service;

use App\Entity\ApiRequest;
use App\Entity\Provider;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HttpRequestService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getRequestData($request, $array = false) {
        if ($request->getContentType() == "json") {
            return json_decode($request->getContent(), $array);
        }
        return $request->request->all();
    }

    public function validateData($entity) {
        $errors = $this->validator->validate($entity);
        if (count($errors) === 0) {
            return true;
        }
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        throw new BadRequestHttpException("Validation failed." . implode(",", $errorMessages));
    }
}