<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\ImageUploadRequest;
use App\Models\CustomField;
use App\Models\Manufacturer;
use Auth;
use Exception;
use Gate;
use Input;
use Lang;
use Redirect;
use Str;
use View;
use Illuminate\Http\Request;
use Image;

/**
 * This controller handles all actions related to Manufacturers for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class ManufacturersController extends Controller
{
    /**
    * Returns a view that invokes the ajax tables which actually contains
    * the content for the manufacturers listing, which is generated in getDatatable.
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @see Api\ManufacturersController::index() method that generates the JSON response
    * @since [v1.0]
    * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorize('index', Manufacturer::class);
        return view('manufacturers/index', compact('manufacturers'));
    }


    /**
    * Returns a view that displays a form to create a new manufacturer.
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @see ManufacturersController::store()
    * @since [v1.0]
    * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->authorize('create', Manufacturer::class);
        return view('manufacturers/edit')->with('item', new Manufacturer);
    }


    /**
     * Validates and stores the data for a new manufacturer.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ManufacturersController::create()
     * @since [v1.0]
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ImageUploadRequest $request)
    {

        $this->authorize('create', Manufacturer::class);
        $manufacturer = new Manufacturer;
        $manufacturer->name            = $request->input('name');
        $manufacturer->user_id          = Auth::user()->id;
        $manufacturer->url     = $request->input('url');
        $manufacturer->support_url     = $request->input('support_url');
        $manufacturer->support_phone    = $request->input('support_phone');
        $manufacturer->support_email    = $request->input('support_email');


        if ($request->file('image')) {
            $image = $request->file('image');
            $file_name = str_slug($image->getClientOriginalName()).".".$image->getClientOriginalExtension();
            $path = public_path('uploads/manufacturers/'.$file_name);
            Image::make($image->getRealPath())->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($path);
            $manufacturer->image = $file_name;
        }



        if ($manufacturer->save()) {
            return redirect()->route('manufacturers.index')->with('success', trans('admin/manufacturers/message.create.success'));
        }
        return redirect()->back()->withInput()->withErrors($manufacturer->getErrors());
    }

    /**
    * Returns a view that displays a form to edit a manufacturer.
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @see ManufacturersController::update()
    * @param int $manufacturerId
    * @since [v1.0]
    * @return \Illuminate\Contracts\View\View
     */
    public function edit($id = null)
    {
        $this->authorize('edit', Manufacturer::class);
        // Check if the manufacturer exists
        if (is_null($item = Manufacturer::find($id))) {
            return redirect()->route('manufacturers.index')->with('error', trans('admin/manufacturers/message.does_not_exist'));
        }
        // Show the page
        return view('manufacturers/edit', compact('item'));
    }


    /**
     * Validates and stores the updated manufacturer data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ManufacturersController::getEdit()
     * @param Request $request
     * @param int $manufacturerId
     * @return \Illuminate\Http\RedirectResponse
     * @since [v1.0]
     */
    public function update(ImageUploadRequest $request, $manufacturerId = null)
    {
        $this->authorize('edit', Manufacturer::class);
        // Check if the manufacturer exists
        if (is_null($manufacturer = Manufacturer::find($manufacturerId))) {
            // Redirect to the manufacturer  page
            return redirect()->route('manufacturers.index')->with('error', trans('admin/manufacturers/message.does_not_exist'));
        }

        // Save the  data
        $manufacturer->name     = $request->input('name');
        $manufacturer->url     = $request->input('url');
        $manufacturer->support_url     = $request->input('support_url');
        $manufacturer->support_phone    = $request->input('support_phone');
        $manufacturer->support_email    = $request->input('support_email');

        $old_image = $manufacturer->image;

        // Set the model's image property to null if the image is being deleted
        if ($request->input('image_delete') == 1) {
            $manufacturer->image = null;
        }

        if ($request->file('image')) {
            $image = $request->file('image');
            $file_name = $manufacturer->id.'-'.str_slug($image->getClientOriginalName()) . "." . $image->getClientOriginalExtension();

            if ($image->getClientOriginalExtension()!='svg') {
                Image::make($image->getRealPath())->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save(app('manufacturers_upload_path').$file_name);
            } else {
                $image->move(app('manufacturers_upload_path'), $file_name);
            }
            $manufacturer->image = $file_name;

        }

        if ((($request->file('image')) && (isset($old_image)) && ($old_image!='')) || ($request->input('image_delete') == 1)) {
            try  {
                unlink(app('manufacturers_upload_path').$old_image);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }


        if ($manufacturer->save()) {
            return redirect()->route('manufacturers.index')->with('success', trans('admin/manufacturers/message.update.success'));
        }
        return redirect()->back()->withInput()->withErrors($manufacturer->getErrors());
    }

    /**
    * Deletes a manufacturer.
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @param int $manufacturerId
    * @since [v1.0]
    * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($manufacturerId)
    {
        $this->authorize('delete', Manufacturer::class);
        // Check if the manufacturer exists
        if (is_null($manufacturer = Manufacturer::find($manufacturerId))) {
            // Redirect to the manufacturers page
            return redirect()->route('manufacturers.index')->with('error', trans('admin/manufacturers/message.not_found'));
        }

        if ($manufacturer->has_models() > 0) {
            // Redirect to the asset management page
            return redirect()->route('manufacturers.index')->with('error', trans('admin/manufacturers/message.assoc_users'));
        }

        if ($manufacturer->image) {
            try  {
                unlink(public_path().'/uploads/manufacturers/'.$manufacturer->image);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }


        // Delete the manufacturer
        $manufacturer->delete();
        // Redirect to the manufacturers management page
        return redirect()->route('manufacturers.index')->with('success', trans('admin/manufacturers/message.delete.success'));
    }

    /**
    * Returns a view that invokes the ajax tables which actually contains
    * the content for the manufacturers detail listing, which is generated via API.
    * This data contains a listing of all assets that belong to that manufacturer.
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @param int $manufacturerId
    * @since [v1.0]
    * @return \Illuminate\Contracts\View\View
     */
    public function show($manufacturerId = null)
    {
        $this->authorize('view', Manufacturer::class);
        $manufacturer = Manufacturer::find($manufacturerId);

        if (isset($manufacturer->id)) {
            return view('manufacturers/view', compact('manufacturer'));
        }

        $error = trans('admin/manufacturers/message.does_not_exist');
        // Redirect to the user management page
        return redirect()->route('manufacturers.index')->with('error', $error);
    }

    /**
     * Restore a given Manufacturer (mark as un-deleted)
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.1.15]
     * @param int $manufacturers_id
     * @return Redirect
     */
    public function restore($manufacturers_id)
    {
        $this->authorize('create', Manufacturer::class);
        $manufacturer = Manufacturer::onlyTrashed()->where('id',$manufacturers_id)->first();

        if ($manufacturer) {

            // Not sure why this is necessary - it shouldn't fail validation here, but it fails without this, so....
            $manufacturer->setValidating(false);
            if ($manufacturer->restore()) {
                return redirect()->route('manufacturers.index')->with('success', trans('admin/manufacturers/message.restore.success'));
            }
            return redirect()->back()->with('error', 'Could not restore.');
        }
        return redirect()->back()->with('error', trans('admin/manufacturers/message.does_not_exist'));

    }

   
    /**
     * Prepeares bulk actions on manufacturers
     * 
     * @param Request $request
     * @return Illuminate\Contracts\View\View
     */
    public function bulk(Request $request)
    {
        $this->authorize('update', Manufacturer::class);

        if (!$request->has('bulk_actions')) {
            return redirect()->back()->with('error', trans('general.bulk_no_action_selected'));
        }

        $action = $request->input('bulk_actions');        

        if (!$request->has('ids')) {
            return redirect()->back()->with('error', trans('admin/manufacturers/form.no_manufactures_selected'));
        }

        $manufacturer_ids = collect($request->input('ids'))->values();

        /**
         * Merging multiple manufacturers together
         */
        if($action == 'merge') {

            /**
             * Get all manufactures to be merged
             */
            $manufacturers = Manufacturer::find($manufacturer_ids);

            $manufacturers_count = $manufacturers->count();

            $ids = $manufacturers->pluck('id');


            /**
             * Prepare selects, with default empty entries
             */
            $names = $manufacturers->pluck('name', 'id')->reject(function ($value) {
                return $value == '';
            });

            $urls = $manufacturers->pluck('url', 'id')->reject(function ($value) {
                return $value == '';
            })->prepend(trans('admin/manufacturers/form.select_url'), '');

            $support_urls = $manufacturers->pluck('support_url', 'id')->reject(function ($value) {
                return $value == '';
            })->prepend(trans('admin/manufacturers/form.select_support_url'), '');

            $support_phones = $manufacturers->pluck('support_phone', 'id')->reject(function ($value) {
                return $value == '';
            })->prepend(trans('admin/manufacturers/form.select_support_phones'), '');

            $support_emails = $manufacturers->pluck('support_email', 'id')->reject(function ($value) {
                return $value == '';
            })->prepend(trans('admin/manufacturers/form.select_support_email'), '');

            $images = $manufacturers->pluck('image', 'id')->reject(function ($value) {
                return $value == '';
            });

            return view('manufacturers/bulk-merge', compact('manufacturers_count', 'ids', 'names', 'urls', 'support_urls', 'support_phones', 'support_emails', 'images'));
        } 
    }


}
