<?php

namespace App\Http\Controllers;

use App\Models\StockCategory;
use App\Models\StockDetail;
use App\Models\StockLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function stockCategoryHandler()
    {
        $stockCategories = StockCategory::orderBy('stock_category')->get();
        return view('stocks.stockscategory', compact('stockCategories'));
        // $accountremainingbalances = CompanyDetails::orderBy('company_name')->get();
        //     $companyDetail = CompanyDetails::find($accountremainingbalances->first()->id);
        //     $accountledgers = $companyDetail->accountLedger;
        //     return view('accounts.parties', compact('accountremainingbalances', 'companyDetail', 'accountledgers'));


        //         $accountremainingbalances = CompanyDetails::orderBy('company_name')->get();

        // if ($accountremainingbalances->isNotEmpty()) {
        //   $companyDetail = CompanyDetails::find($accountremainingbalances->first()->id);
        //   $accountledgers = $companyDetail->accountLedger;
        // } else {
        //   $companyDetail = null;


        //   $accountledgers = null;
        // }

        // return view('accounts.parties', compact('accountremainingbalances', 'companyDetail', 'accountledgers'));

    }
    public function addCategory(Request $request)
    {
        $request->validate([
            'categoryname' => 'required|unique:stock_categories,stock_category',
        ]);

        StockCategory::create([
            'stock_category' => $request->categoryname
        ]);
        return redirect('stocks/categories' . $request->companyname)->with('success', 'New stock category added successfully');
    }
    public function stockHandler($categoryname)
    {
        $stockremainingbalances = StockDetail::orderBy('stock_name')->get();

        $stockDetail = null;
        $stockledgers = null;

        if (!$stockremainingbalances->isEmpty()) {
            $stockDetail = StockDetail::find($stockremainingbalances->first()->id);
            $stockledgers = $stockDetail->stockLedger;
        }
        return view('stocks.stocks', compact('stockremainingbalances', 'stockDetail', 'stockledgers'));

        if ($categoryname == 'AllProduct') {
        } else {
        }
        return view('stocks.stocks');
    }
    public function addNewStock(Request $request)
    {

        $request->validate([
            'stockname' => 'required|unique:stock_details,stock_name'
        ]);

        $stockInformation = StockDetail::create([
            'stock_name' => $request->stockname,
            'limit' => $request->limit ?? 0,
            'opening_balance' => $request->openingbalance ?? 0,
            'date' => $request->date ?? Carbon::now(),
            'category' => $request->category ?? 'fruits',
            'purchase_price' => $request->purchaseprice ?? 0,
            'sales_price' => $request->purchaseprice ?? 0
        ]);

        $stockInformation->stockRemainingBalance()->create([
            'stock_detail_id' => $stockInformation->id,
            'date' => $request->date ?? Carbon::now(),
            'quantity' => $request->openingbalance ?? 0
        ]);

        // return redirect('/parties/viewledger/' . $request->companyname)->with('success', 'New stock added successfully');
        return redirect('/stocks-list/AllProduct')->with('success', 'New stock added successfully');
    }
    public function stockSales(Request $request)
    {
        $request->validate([
            'productname' => 'required|exists:stock_details,stock_name',
            'issuedquantity' => 'required'
        ]);
        $stockInformation = StockDetail::where('stock_name', $request->productname)->firstOrFail();
        $stockInformation->stockRemainingBalance()->decrement('quantity', $request->issuedquantity);
        $stockInformation->stockledger()->create([
            'date' => $request->date ?? Carbon::now(),
            'particulars' => 'sales',
            'receipt_no' => $request->billno,
            'issued_quantity' => $request->issuedquantity
        ]);
        return redirect('/stocks-list/AllProduct')->with('success', 'Sales of goods recorded successfully');
    }
    public function stockPurchase(Request $request)
    {
        $request->validate([
            'productname' => 'required|exists:stock_details,stock_name',
            'purchasequantity' => 'required'
        ]);
        $stockInformation = StockDetail::where('stock_name', $request->productname)->firstOrFail();
        $stockInformation->stockRemainingBalance()->increment('quantity', $request->purchasequantity);
        $stockInformation->stockledger()->create([
            'date' => $request->date ?? Carbon::now(),
            'particulars' => 'purchase',
            'receipt_no' => $request->billno,
            'quantity' => $request->purchasequantity,
            'rate' => $request->rate,
        ]);
        return redirect('/stocks-list/AllProduct')->with('success', 'Purchase of goods recorded successfully');
    }
    public function viewStockLedger($stockname)
    {
        $stockremainingbalances = StockDetail::orderBy('stock_name')->get();
        $stockDetail = StockDetail::where('stock_name', $stockname)->firstOrFail();
        $stockledgers = $stockDetail->stockLedger;
        return view('stocks.stocks', compact('stockremainingbalances', 'stockDetail', 'stockledgers'));
    }
    public function editStockDetails(Request $request)
    {
        $request->validate([
            // 'stockname' => 'required|unique:stock_details,stock_name'
            'stockname' => 'required'
        ]);

        $stockDetail = StockDetail::find($request->stockID);
        $stockDetail->stockRemainingBalance()->decrement('quantity', $stockDetail->opening_balance);

        $stockDetail->update([
            'stock_name' => $request->stockname,
            'limit' => $request->limit ?? 0,
            'opening_balance' => $request->openingbalance ?? 0,
            'date' => $request->date ?? Carbon::now(),
            'category' => $request->category ?? 'fruits',
            'purchase_price' => $request->purchaseprice ?? 0,
            'sales_price' => $request->purchaseprice ?? 0
        ]);
        $stockDetail->stockRemainingBalance()->increment('quantity', $request->openingbalance);
        return redirect('/stocks-list/AllProduct')->with('success', 'Purchase of goods recorded successfully');
    }
    public function editStockLedgerDetails($id, $stockname)
    {
        $stockledger = StockLedger::find($id);
        dd($stockledger);
    }
}
