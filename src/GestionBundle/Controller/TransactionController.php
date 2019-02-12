<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;


/**
 * Transaction controller.
 *
 * @Route("transaction")
 */
class TransactionController extends Controller
{
    /**
     * Lists all transaction entities.
     *
     * @Route("/", name="transaction_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('GestionBundle:Transaction')->findAll();

        return $this->render('@Gestion/transaction/index.html.twig', array(
            'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new transaction entity.
     *
     * @Route("/new", name="transaction_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $transaction = new Transaction();
        $form = $this->createForm('GestionBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $transaction->setCreatedAt(new \DateTime());
            $transaction->setUpdatedAt(new \DateTime());
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
        }

        return $this->render('@Gestion/transaction/new.html.twig', array(
            'transaction' => $transaction,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a transaction entity.
     *
     * @Route("/{id}", name="transaction_show")
     * @Method("GET")
     */
    public function showAction(Transaction $transaction)
    {

        return $this->render('@Gestion/transaction/show.html.twig', array(
            'transaction' => $transaction,
        ));
    }

    /**
     * Displays a form to edit an existing transaction entity.
     *
     * @Route("/{id}/edit", name="transaction_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        $editForm = $this->createForm('GestionBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $transaction->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_edit', array('id' => $transaction->getId()));
        }

        return $this->render('@Gestion/transaction/edit.html.twig', array(
            'transaction' => $transaction,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a transaction entity.
     *
     * @Route(name="transaction_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $transaction = $em->getRepository('GestionBundle:Transaction')->find($id);
        $em->remove($transaction);
        $em->flush();
        return new Response(json_encode(array('status'=>'success')));
    }


    /**
     * search transaction per month.
     *
     * @Route("/search/", name="transaction_search")
     * @Method("GET")
     */
    public function searchMonthAction(Request $request )
    {
            $transaction = new Transaction();
            
            $transaction->setCreatedAt(new \DateTime('now') );
            $form = $this->createFormBuilder($transaction)
              ->add('created_at', DateType::class, array(
                    'widget' => 'single_text',

                    // prevents rendering it as type="date", to avoid HTML5 date pickers
                    'html5' => false,
                    'format' => 'yyyy-MM',

                    // adds a class that can be selected in JavaScript
                    'attr' => ['class' => 'date-picker'],
                ))
             ->getForm();            
            $form->handleRequest($request);
            return $this->render('@Gestion/transaction/search.html.twig', array(
                'form' => $form->createView(),
            ));
    }


    /**
     * search transaction per month.
     *
     * @Route("/search/ajax/", name="transaction_search_result_ajax")
     * @Method("GET")
     */
    public function searchMonthResultAjaxAction(Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $date = $request->query->get('date');
         
        $first_day_this_month = date($date.'-01');

        $firstDayThisMonth = new \DateTime($first_day_this_month);

        $lastDayThisMonth = new \DateTime($firstDayThisMonth->format('Y-m-t'));
        $lastDayThisMonth->setTime(23, 59, 59);

        /*echo $firstDayThisMonth->format("Y-m-d");
        echo "<->";
        echo $lastDayThisMonth->format("Y-m-d");*/

        $query = $em->createQuery(
            'SELECT r
            FROM GestionBundle:Transaction r            
            WHERE r.createdAt BETWEEN :firstDayThisMonth AND :lastDayThisMonth
            and r.isValid = :is_valid
            order by r.createdAt DESC
            '
        )
        ->setParameter('firstDayThisMonth', $firstDayThisMonth)
        ->setParameter('lastDayThisMonth', $lastDayThisMonth)
        ->setParameter('is_valid', TRUE)
        ;

        $transactions = $query->getResult(); 
        $sumInputThisMonth = $em->getRepository('GestionBundle:Transaction')->sumInputByMonth($firstDayThisMonth,$lastDayThisMonth);
        $sumOutputThisMonth = $em->getRepository('GestionBundle:Transaction')->sumOutputByMonth($firstDayThisMonth,$lastDayThisMonth);

        $sumInputBeforeMonth = $em->getRepository('GestionBundle:Transaction')->sumInputBeforeMonth($firstDayThisMonth);
        $sumOutputBeforeMonth = $em->getRepository('GestionBundle:Transaction')->sumOutputBeforeMonth($firstDayThisMonth);
        //var_dump($sumOutputBeforeMonth);
        $tresorieStartMonth = $sumInputBeforeMonth - $sumOutputBeforeMonth;
        $tresorieAfterMonth = $tresorieStartMonth + $sumInputThisMonth - $sumOutputThisMonth;

        return $this->render('@Gestion/transaction/searchResult.html.twig', array(
            'transactions' => $transactions,
            'sumInputThisMonth' => $sumInputThisMonth,
            'sumOutputThisMonth' => $sumOutputThisMonth,
            'tresorieStartMonth' => $tresorieStartMonth,
            'tresorieAfterMonth' => $tresorieAfterMonth
        ));

        //die('ok');

    }


}
