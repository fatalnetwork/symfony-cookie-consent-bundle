<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace FatalNetwork\CookieConsentBundle\Controller;

use FatalNetwork\CookieConsentBundle\Cookie\CookieChecker;
use FatalNetwork\CookieConsentBundle\Form\CookieConsentType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class CookieConsentController
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CookieChecker
     */
    private $cookieChecker;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string|null
     */
    private $formAction;

    /**
     * @var array
     */
    private $disabledRoutes;

    public function __construct(
        Environment $twigEnvironment,
        FormFactoryInterface $formFactory,
        CookieChecker $cookieChecker,
        RouterInterface $router,
        TranslatorInterface $translator,
        string $formAction = null,
        array $cookieConsentDisabledRoutes = []
    ) {
        $this->twigEnvironment         = $twigEnvironment;
        $this->formFactory             = $formFactory;
        $this->cookieChecker           = $cookieChecker;
        $this->router                  = $router;
        $this->translator              = $translator;
        $this->formAction              = $formAction;
        $this->disabledRoutes          = $cookieConsentDisabledRoutes;
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent", name="fn_cookie_consent.show")
     */
    public function show(Request $request): Response
    {
        $this->setLocale($request);

        $response = new Response(
            $this->twigEnvironment->render('@FNCookieConsent/cookie_consent.html.twig', [
                'form'       => $this->createCookieConsentForm()->createView(),
                'disabled_routes' => $this->disabledRoutes,
            ])
        );

        // Cache in ESI should not be shared
        $response->setPrivate();
        $response->setMaxAge(0);

        return $response;
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent_alt", name="fn_cookie_consent.show_if_cookie_consent_not_set")
     */
    public function showIfCookieConsentNotSet(Request $request): Response
    {
        if ($this->cookieChecker->isCookieConsentSavedByUser() === false) {
            return $this->show($request);
        }

        return new Response();
    }

    /**
     * Create cookie consent form.
     */
    protected function createCookieConsentForm(): FormInterface
    {
        if ($this->formAction === null) {
            $form = $this->formFactory->create(CookieConsentType::class);
        } else {
            $form = $this->formFactory->create(
                CookieConsentType::class,
                null,
                [
                    'action' => $this->router->generate($this->formAction),
                ]
            );
        }

        return $form;
    }

    /**
     * Set locale if available as GET parameter.
     */
    protected function setLocale(Request $request)
    {
        $locale = $request->get('locale');
        if (empty($locale) === false) {
            $this->translator->setLocale($locale);
            $request->setLocale($locale);
        }
    }
}
